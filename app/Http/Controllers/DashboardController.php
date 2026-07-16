<?php

namespace App\Http\Controllers;

use App\Models\ActivitePedagogique;
use App\Models\AffectationCours;
use App\Models\AnneeAcademique;
use App\Models\Cours;
use App\Models\Departement;
use App\Models\Enseignant;
use App\Models\ParametreCalcul;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (function_exists('logActivite')) {
            logActivite('consultation', 'Accès au tableau de bord');
        }

        $utilisateur = Auth::user();
        $role = $utilisateur->role;

        // Rediriger vers le dashboard approprié selon le rôle
        return match ($role->code) {
            'admin'      => $this->adminDashboard(),
            'secretaire' => $this->secretaireDashboard(),
            'enseignant' => $this->enseignantDashboard(),
            default      => $this->adminDashboard(),
        };
    }

    // ─── Dashboard Administrateur ───────────────────────────────────────────

    private function adminDashboard()
    {
        $currentYear = AnneeAcademique::where('statut', 'en_cours')->first();
        $currentYearId = $currentYear?->id;

        $params = $currentYearId ? ParametreCalcul::where('annee_id', $currentYearId)->first() : null;
        $serviceStatutaire = $params?->service_statutaire ?? 192;

        // Statistiques générales
        $stats = [
            'enseignants'            => Enseignant::count(),
            'cours_actifs'           => Cours::whereHas('affectationsCours', function ($q) use ($currentYearId) {
                $q->where('id_annee', $currentYearId);
            })->count(),
            'volume_horaire_total'   => ActivitePedagogique::where('statut', 'validee')
                ->whereHas('affectationCours', function ($q) use ($currentYearId) {
                    $q->where('id_annee', $currentYearId);
                })
                ->sum('volume_horaire'),
            'heures_complementaires' => 0,
            'activites_en_attente'   => ActivitePedagogique::where('statut', 'en_attente')
                ->whereHas('affectationCours', function ($q) use ($currentYearId) {
                    $q->where('id_annee', $currentYearId);
                })
                ->count(),
        ];

        // Volume par département
        $volumesParDepartement = Departement::withCount(['enseignants' => function ($q) {
            $q->whereHas('affectationsCours.activitesPedagogiques', function ($q2) {
                $q2->where('statut', 'validee');
            });
        }])
            ->get()
            ->map(function ($dep) use ($currentYearId) {
                $vht = ActivitePedagogique::where('statut', 'validee')
                    ->whereHas('affectationCours', function ($q) use ($currentYearId, $dep) {
                        $q->where('id_annee', $currentYearId)
                            ->whereHas('enseignant', function ($q2) use ($dep) {
                                $q2->where('id_departement', $dep->id);
                            });
                    })
                    ->sum('volume_horaire');
                return [
                    'departement'       => $dep->nom_departement,
                    'code'              => $dep->code_departement,
                    'volume'            => round($vht, 1),
                    'enseignants_count' => $dep->enseignants_count,
                ];
            });

        $maxVolume = $volumesParDepartement->max('volume') ?: 1;
        $chartLabels = $volumesParDepartement->pluck('code')->toArray();
        $chartData = $volumesParDepartement->pluck('volume')->toArray();

        // Répartition des activités
        $activitesParType = ActivitePedagogique::select('type_activite', 'id_niveau', DB::raw('COUNT(*) as total'))
            ->whereHas('affectationCours', function ($q) use ($currentYearId) {
                $q->where('id_annee', $currentYearId);
            })
            ->groupBy('type_activite', 'id_niveau')
            ->with('niveauComplexite')
            ->get();

        $totalActivites = $activitesParType->sum('total') ?: 1;

        $repartitionActivites = $activitesParType->map(function ($item) use ($totalActivites) {
            $label = ($item->type_activite === 'creation' ? 'Création' : 'Mise à jour')
                . ' — ' . ($item->niveauComplexite?->libelle ?? 'N/A');
            return [
                'label' => $label,
                'count' => $item->total,
                'pct'   => round($item->total / $totalActivites * 100),
            ];
        });

        // Activités récentes
        $activitesRecentes = ActivitePedagogique::with([
            'affectationCours.enseignant.utilisateur',
            'affectationCours.cours',
            'niveauComplexite',
        ])
            ->whereHas('affectationCours', function ($q) use ($currentYearId) {
                $q->where('id_annee', $currentYearId);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($a) {
                $typeLabel = $a->type_activite === 'creation' ? 'Création' : 'Mise à jour';
                $niveauLabel = $a->niveauComplexite?->libelle ?? '';
                return [
                    'enseignant'    => $a->affectationCours?->enseignant?->utilisateur?->prenom . ' ' . $a->affectationCours?->enseignant?->utilisateur?->nom,
                    'type'          => $typeLabel . ' — ' . $niveauLabel,
                    'cours'         => $a->affectationCours?->cours?->code_cours,
                    'volume'        => $a->volume_horaire . 'h',
                    'statut'        => $a->statut,
                    'statut_badge'  => match ($a->statut) {
                        'validee'   => 'green',
                        'en_attente' => 'amber',
                        'rejetee'   => 'red',
                        default     => 'gray',
                    },
                ];
            });

        // Enseignants en dépassement
        $enseignants = Enseignant::with('utilisateur', 'affectationsCours.activitesPedagogiques')
            ->whereHas('affectationsCours', function ($q) use ($currentYearId) {
                $q->where('id_annee', $currentYearId);
            })
            ->get()
            ->map(function ($e) use ($serviceStatutaire) {
                $vhtTotal = $e->affectationsCours->flatMap(function ($aff) {
                    return $aff->activitesPedagogiques->where('statut', 'validee');
                })->sum('volume_horaire');
                $complementaires = max(0, $vhtTotal - $serviceStatutaire);
                return [
                    'nom'            => $e->utilisateur?->prenom . ' ' . $e->utilisateur?->nom,
                    'vht'            => round($vhtTotal, 1),
                    'service'        => $serviceStatutaire,
                    'complementaires' => round($complementaires, 1),
                    'depassement'    => $complementaires > 0,
                ];
            })
            ->filter(fn($e) => $e['depassement'])
            ->sortByDesc('complementaires')
            ->take(5)
            ->values();

        $stats['heures_complementaires'] = round($enseignants->sum('complementaires'), 1);

        return view('dashboard-admin', compact(
            'stats',
            'currentYear',
            'volumesParDepartement',
            'maxVolume',
            'repartitionActivites',
            'activitesRecentes',
            'enseignants',
            'serviceStatutaire',
            'chartLabels',
            'chartData'
        ));
    }

    // ─── Dashboard Secrétaire ───────────────────────────────────────────────

    private function secretaireDashboard()
    {
        $currentYear = AnneeAcademique::where('statut', 'en_cours')->first();
        $currentYearId = $currentYear?->id;

        // Statistiques pour le secrétaire
        $stats = [
            'enseignants'          => Enseignant::count(),
            'cours_actifs'         => Cours::whereHas('affectationsCours', function ($q) use ($currentYearId) {
                $q->where('id_annee', $currentYearId);
            })->count(),
            'affectations'         => AffectationCours::where('id_annee', $currentYearId)->count(),
            'activites_en_attente' => ActivitePedagogique::where('statut', 'en_cours')
                ->whereHas('affectationCours', function ($q) use ($currentYearId) {
                    $q->where('id_annee', $currentYearId);
                })
                ->count(),
        ];

        // Activités en attente de validation
        $activitesEnAttente = ActivitePedagogique::with([
            'affectationCours.enseignant.utilisateur',
            'affectationCours.cours',
            'niveauComplexite',
        ])
            ->where('statut', 'en_cours')
            ->whereHas('affectationCours', function ($q) use ($currentYearId) {
                $q->where('id_annee', $currentYearId);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($a) {
                return [
                    'id'            => $a->id,
                    'enseignant'    => $a->affectationCours?->enseignant?->utilisateur?->prenom . ' ' . $a->affectationCours?->enseignant?->utilisateur?->nom,
                    'cours'         => $a->affectationCours?->cours?->code_cours . ' - ' . $a->affectationCours?->cours?->intitule,
                    'type'          => $a->type_activite === 'creation' ? 'Création' : 'Mise à jour',
                    'volume'        => $a->volume_horaire . 'h',
                    'date'          => $a->date_activite->format('d/m/Y'),
                ];
            });

        // Dernières affectations
        $dernieresAffectations = AffectationCours::with([
            'enseignant.utilisateur',
            'cours',
        ])
            ->where('id_annee', $currentYearId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($a) {
                return [
                    'enseignant' => $a->enseignant?->utilisateur?->prenom . ' ' . $a->enseignant?->utilisateur?->nom,
                    'cours'      => $a->cours?->code_cours . ' - ' . $a->cours?->intitule,
                    'niveau'     => $a->niveau,
                    'semestre'   => $a->semestre,
                    'volume'     => $a->volume_horaire . 'h',
                ];
            });

        return view('dashboard-secretaire', compact(
            'stats',
            'currentYear',
            'activitesEnAttente',
            'dernieresAffectations'
        ));
    }

    // ─── Dashboard Enseignant ───────────────────────────────────────────────

    private function enseignantDashboard()
    {
        $enseignant = Auth::user()->enseignant;

        if (!$enseignant) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun profil enseignant associé à votre compte.');
        }

        $currentYear = AnneeAcademique::where('statut', 'en_cours')->first();
        $currentYearId = $currentYear?->id;

        $params = $currentYearId ? ParametreCalcul::where('annee_id', $currentYearId)->first() : null;
        $serviceStatutaire = $params?->service_statutaire ?? 192;

        // Affectations de l'enseignant
        $affectations = AffectationCours::with('cours')
            ->where('id_enseignant', $enseignant->id)
            ->where('id_annee', $currentYearId)
            ->get();

        // Activités de l'enseignant
        $activites = ActivitePedagogique::with([
            'affectationCours.cours',
            'niveauComplexite',
        ])
            ->whereHas('affectationCours', function ($q) use ($enseignant) {
                $q->where('id_enseignant', $enseignant->id);
            })
            ->orderBy('date_activite', 'desc')
            ->get();

        $volumeRealise = $activites->where('statut', 'validee')->sum('volume_horaire');
        $heuresComplementaires = max(0, $volumeRealise - $serviceStatutaire);

        // Volume total prévu (basé sur les affectations et les heures des cours)
        $volumeTotal = 0;
        foreach ($affectations as $affectation) {
            if ($affectation->cours) {
                $volumeTotal += $affectation->cours->nombre_heures;
            }
        }

        // Stats personnelles
        $stats = [
            'cours_assignes'        => $affectations->count(),
            'volume_total'          => $volumeTotal,
            'volume_realise'        => $volumeRealise,
            'heures_complementaires' => $heuresComplementaires,
            'activites_en_cours'    => $activites->where('statut', 'en_cours')->count(),
            'activites_validees'    => $activites->where('statut', 'validee')->count(),
            'activites_rejetees'    => $activites->where('statut', 'rejetee')->count(),
            'taux_realisation'      => $serviceStatutaire > 0
                ? round(($volumeRealise / $serviceStatutaire) * 100)
                : 0,
        ];

        // Activités récentes (5 dernières)
        $activitesRecentes = $activites->take(5)->map(function ($a) {
            return [
                'date'   => $a->date_activite->format('d/m/Y'),
                'cours'  => $a->affectationCours?->cours?->code_cours . ' - ' . $a->affectationCours?->cours?->intitule,
                'type'   => $a->type_activite === 'creation' ? 'Création' : 'Mise à jour',
                'volume' => $a->volume_horaire . 'h',
                'statut' => $a->statut,
                'badge'  => match ($a->statut) {
                    'validee'   => 'green',
                    'en_attente' => 'amber',
                    'rejetee'   => 'red',
                    default     => 'gray',
                },
            ];
        });

        return view('dashboard-enseignant', compact(
            'stats',
            'currentYear',
            'affectations',
            'activitesRecentes',
            'serviceStatutaire',
            'volumeRealise',
            'heuresComplementaires'
        ));
    }
}
