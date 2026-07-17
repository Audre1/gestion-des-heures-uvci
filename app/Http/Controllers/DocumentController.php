<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivitePedagogique;
use App\Models\ParametreCalcul;
use App\Models\AnneeAcademique;


class DocumentController extends Controller
{
    private function logo()
    {
        $logoPath = public_path('images/logo-simple.png');
        return file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    }

    public function recapitulatifActivites()
    {
        $idEnseignant = Auth::user()->enseignant->id;
        $anneeId = request()->get('annee');

        // Récupérer l'année académique ou utiliser l'année active par défaut
        $annee = $anneeId ? AnneeAcademique::find($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        $activites = ActivitePedagogique::query()
            ->whereHas('affectationCours', function ($query) use ($idEnseignant, $annee) {
                $query->where('id_enseignant', $idEnseignant);
                if ($annee) {
                    $query->where('id_annee', $annee->id);
                }
            })
            ->with([
                'affectationCours.cours',
                'niveauComplexite',
            ])
            ->orderByDesc('date_activite')
            ->get();

        if (function_exists('logActivite')) {
            logActivite('téléchargement', 'Téléchargement du récapitulatif des activités pédagogiques');
        }

        // Formater les données pour le template générique
        $headers = ['Date', 'Cours', 'Type', 'Volume (h)', 'Statut'];
        $rows = $activites->map(function ($activite) {
            return [
                $activite->date_activite->format('d/m/Y'),
                $activite->affectationCours->cours->code_cours . ' - ' . $activite->affectationCours->cours->intitule,
                $activite->type_activite === 'creation' ? 'Création' : 'Mise à jour',
                $activite->volume_horaire,
                ucfirst(str_replace('_', ' ', $activite->statut)),
            ];
        })->toArray();

        $anneeLibelle = $annee ? $annee->libelle : 'Toutes années';
        $anneeSlug = $annee ? str_replace('/', '-', $annee->libelle) : 'toutes-annees';

        $pdf = Pdf::loadView('exports.pdf-template', [
            'title' => 'Récapitulatif des Activités Pédagogiques - ' . $anneeLibelle,
            'date' => now()->format('d/m/Y H:i'),
            'logo' => $this->logo(),
            'headers' => $headers,
            'rows' => $rows,
        ]);

        return $pdf->download('recapitulatif-activites-' . $anneeSlug . '.pdf');
    }


    public function ficheIndividuelle()
    {
        $enseignant = Auth::user()->enseignant;
        $idEnseignant = $enseignant->id;
        $anneeId = request()->get('annee');

        // Récupérer l'année académique ou utiliser l'année active par défaut
        $annee = $anneeId ? AnneeAcademique::find($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        // Récupérer le service statutaire depuis les paramètres de calcul de l'année
        // Uniquement pour les permanents
        $params = $annee ? ParametreCalcul::where('annee_id', $annee->id)->first() : null;
        $serviceStatutaire = ($enseignant->statut === 'Permanent')
            ? ($params ? $params->service_statutaire : 192)
            : 0; // Pour les vacataires, pas de service statutaire

        $activites = ActivitePedagogique::whereHas('affectationCours', function ($query) use ($idEnseignant, $annee) {
            $query->where('id_enseignant', $idEnseignant);
            if ($annee) {
                $query->where('id_annee', $annee->id);
            }
        })
            ->where('statut', 'validee')
            ->get();

        $volumeRealise = $activites->sum('volume_horaire');

        // Calcul des heures complémentaires selon le statut
        $heuresComplementaires = ($enseignant->statut === 'Permanent')
            ? max(0, $volumeRealise - $serviceStatutaire)
            : $volumeRealise; // Pour les vacataires, tout est complémentaire

        if (function_exists('logActivite')) {
            logActivite('téléchargement', 'Téléchargement de la fiche individuelle');
        }

        // Formater les données pour le template générique
        $headers = ['Informations', 'Valeurs'];
        $rows = [
            ['Nom complet', $enseignant->utilisateur->prenom . ' ' . $enseignant->utilisateur->nom],
            ['Grade', $enseignant->grade->libelle ?? '-'],
            ['Département', $enseignant->departement->nom_departement ?? '-'],
            ['Année académique', $annee ? $annee->libelle : '-'],
            ['Service statutaire', $serviceStatutaire . ' h'],
            ['Volume réalisé', $volumeRealise . ' h'],
            ['Heures complémentaires', $heuresComplementaires . ' h'],
            ['Charge globale', $serviceStatutaire > 0 ? round(($volumeRealise / $serviceStatutaire) * 100) . '%' : '0%'],
        ];

        $anneeLibelle = $annee ? $annee->libelle : 'Toutes années';
        $anneeSlug = $annee ? str_replace('/', '-', $annee->libelle) : 'toutes-annees';

        $pdf = Pdf::loadView('exports.pdf-template', [
            'title' => 'Fiche Individuelle Enseignant - ' . $anneeLibelle,
            'date' => now()->format('d/m/Y H:i'),
            'logo' => $this->logo(),
            'headers' => $headers,
            'rows' => $rows,
        ]);

        return $pdf->download('fiche-individuelle-' . $anneeSlug . '.pdf');
    }

    public function etatHeures()
    {
        $enseignant = Auth::user()->enseignant;
        $idEnseignant = $enseignant->id;
        $anneeId = request()->get('annee');

        // Récupérer l'année académique ou utiliser l'année active par défaut
        $annee = $anneeId ? AnneeAcademique::find($anneeId) : AnneeAcademique::where('statut', 'en_cours')->first();

        // Récupérer le service statutaire depuis les paramètres de calcul de l'année
        // Uniquement pour les permanents
        $params = $annee ? ParametreCalcul::where('annee_id', $annee->id)->first() : null;
        $serviceStatutaire = ($enseignant->statut === 'Permanent')
            ? ($params ? $params->service_statutaire : 192)
            : 0; // Pour les vacataires, pas de service statutaire

        $activites = ActivitePedagogique::whereHas('affectationCours', function ($query) use ($idEnseignant, $annee) {
            $query->where('id_enseignant', $idEnseignant);
            if ($annee) {
                $query->where('id_annee', $annee->id);
            }
        })
            ->where('statut', 'validee')
            ->with([
                'affectationCours.cours'
            ])
            ->get();

        $volumeRealise = $activites->sum('volume_horaire');

        // Calcul des heures complémentaires selon le statut
        $heuresComplementaires = ($enseignant->statut === 'Permanent')
            ? max(0, $volumeRealise - $serviceStatutaire)
            : $volumeRealise; // Pour les vacataires, tout est complémentaire

        if (function_exists('logActivite')) {
            logActivite('téléchargement', 'Téléchargement de l\'état des heures');
        }

        // Formater les données pour le template générique
        $headers = ['Date', 'Cours', 'Volume (h)', 'Statut'];
        $rows = $activites->map(function ($activite) {
            return [
                $activite->date_activite->format('d/m/Y'),
                $activite->affectationCours->cours->code_cours . ' - ' . $activite->affectationCours->cours->intitule,
                $activite->volume_horaire,
                ucfirst(str_replace('_', ' ', $activite->statut)),
            ];
        })->toArray();

        // Ajouter le récapitulatif en bas
        $rows[] = ['', '', '', ''];
        $rows[] = ['Total réalisé', $volumeRealise . ' h', '', ''];
        $rows[] = ['Service statutaire', $serviceStatutaire . ' h', '', ''];
        $rows[] = ['Heures complémentaires', $heuresComplementaires . ' h', '', ''];

        $anneeLibelle = $annee ? $annee->libelle : 'Toutes années';
        $anneeSlug = $annee ? str_replace('/', '-', $annee->libelle) : 'toutes-annees';

        $pdf = Pdf::loadView('exports.pdf-template', [
            'title' => 'État des Heures - ' . $anneeLibelle,
            'date' => now()->format('d/m/Y H:i'),
            'logo' => $this->logo(),
            'headers' => $headers,
            'rows' => $rows,
        ]);

        return $pdf->download('etat-heures-' . $anneeSlug . '.pdf');
    }
}
