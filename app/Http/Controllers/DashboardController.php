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

        // Année académique en cours
        $currentYear = AnneeAcademique::where('statut', 'en_cours')->first();
        $currentYearId = $currentYear?->id;

        // Paramètres de calcul
        $params = $currentYearId ? ParametreCalcul::where('annee_id', $currentYearId)->first() : null;
        $serviceStatutaire = $params?->service_statutaire ?? 192;

        // ─── Statistiques générales ────────────────────────────────────────
        $stats = [
            'enseignants' => Enseignant::count(),
            'cours_actifs' => Cours::whereHas('affectationsCours', function ($q) use ($currentYearId) {
                $q->where('id_annee', $currentYearId);
            })->count(),
            'volume_horaire_total' => ActivitePedagogique::where('statut', 'validee')
                ->whereHas('affectationCours', function ($q) use ($currentYearId) {
                    $q->where('id_annee', $currentYearId);
                })
                ->sum('volume_horaire'),
            'heures_complementaires' => 0, // Calculé plus bas
        ];

        // ─── Volume horaire par département ────────────────────────────────
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
                    'departement' => $dep->nom_departement,
                    'code' => $dep->code_departement,
                    'volume' => round($vht, 1),
                    'enseignants_count' => $dep->enseignants_count,
                ];
            });

        $maxVolume = $volumesParDepartement->max('volume') ?: 1;

        // ─── Répartition des activités par type et niveau ──────────────────
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
                'pct' => round($item->total / $totalActivites * 100),
            ];
        });

        // ─── Activités récentes ────────────────────────────────────────────
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
                    'enseignant' => $a->affectationCours?->enseignant?->utilisateur?->prenom . ' ' . $a->affectationCours?->enseignant?->utilisateur?->nom,
                    'type' => $typeLabel . ' — ' . $niveauLabel,
                    'cours' => $a->affectationCours?->cours?->code_cours,
                    'volume' => $a->volume_horaire . 'h',
                    'statut' => $a->statut,
                    'statut_badge' => match($a->statut) {
                        'validee' => 'green',
                        'en_attente' => 'amber',
                        'rejetee' => 'red',
                        default => 'gray',
                    },
                ];
            });

        // ─── Enseignants en dépassement de charge ──────────────────────────
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
                    'nom' => $e->utilisateur?->prenom . ' ' . $e->utilisateur?->nom,
                    'vht' => round($vhtTotal, 1),
                    'service' => $serviceStatutaire,
                    'complementaires' => round($complementaires, 1),
                    'depassement' => $complementaires > 0,
                ];
            })
            ->filter(fn($e) => $e['depassement'])
            ->sortByDesc('complementaires')
            ->take(5)
            ->values();

        // Mettre à jour les heures complémentaires totales
        $stats['heures_complementaires'] = round($enseignants->sum('complementaires'), 1);

        return view('dashboard', compact(
            'stats',
            'currentYear',
            'volumesParDepartement',
            'maxVolume',
            'repartitionActivites',
            'activitesRecentes',
            'enseignants',
            'serviceStatutaire',
        ));
    }
}