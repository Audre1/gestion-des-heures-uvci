<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivitePedagogique;
use App\Models\ParametreCalcul;


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



        $activites = ActivitePedagogique::query()
            ->whereHas('affectationCours', function ($query) use ($idEnseignant) {
                $query->where('id_enseignant', $idEnseignant);
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
        $headers = ['Date', 'Cours', 'Type', 'Niveau', 'Complexité', 'Volume (h)', 'Statut'];
        $rows = $activites->map(function ($activite) {
            return [
                $activite->date_activite->format('d/m/Y'),
                $activite->affectationCours->cours->code_cours . ' - ' . $activite->affectationCours->cours->intitule,
                $activite->type_activite === 'creation' ? 'Création' : 'Mise à jour',
                $activite->affectationCours->niveau ?? '-',
                $activite->niveauComplexite->libelle ?? '-',
                $activite->volume_horaire,
                ucfirst($activite->statut),
            ];
        })->toArray();

        $pdf = Pdf::loadView('exports.pdf-template', [
            'title' => 'Récapitulatif des Activités Pédagogiques',
            'date' => now()->format('d/m/Y H:i'),
            'logo' => $this->logo(),
            'headers' => $headers,
            'rows' => $rows,
        ]);

        return $pdf->download('recapitulatif-activites.pdf');
    }


    public function ficheIndividuelle()
    {
        $enseignant = Auth::user()->enseignant;

        $idEnseignant = $enseignant->id;

        // Récupérer le service statutaire depuis les paramètres de calcul de l'année active
        $params = ParametreCalcul::anneeActive()->first();
        $serviceStatutaire = $params ? $params->service_statutaire : 10;

        $activites = ActivitePedagogique::whereHas('affectationCours', function ($query) use ($idEnseignant) {
            $query->where('id_enseignant', $idEnseignant);
        })
            ->where('statut', 'validee')
            ->get();

        $volumeRealise = $activites->sum('volume_horaire');

        $heuresComplementaires = max(0, $volumeRealise - $serviceStatutaire);

        if (function_exists('logActivite')) {
            logActivite('téléchargement', 'Téléchargement de la fiche individuelle');
        }

        // Formater les données pour le template générique
        $headers = ['Informations', 'Valeurs'];
        $rows = [
            ['Nom complet', $enseignant->utilisateur->prenom . ' ' . $enseignant->utilisateur->nom],
            ['Grade', $enseignant->grade->libelle ?? '-'],
            ['Département', $enseignant->departement->nom_departement ?? '-'],
            ['Service statutaire', $serviceStatutaire . ' h'],
            ['Volume réalisé', $volumeRealise . ' h'],
            ['Heures complémentaires', $heuresComplementaires . ' h'],
            ['Charge globale', $serviceStatutaire > 0 ? round(($volumeRealise / $serviceStatutaire) * 100) . '%' : '0%'],
        ];

        $pdf = Pdf::loadView('exports.pdf-template', [
            'title' => 'Fiche Individuelle Enseignant',
            'date' => now()->format('d/m/Y H:i'),
            'logo' => $this->logo(),
            'headers' => $headers,
            'rows' => $rows,
        ]);

        return $pdf->download('fiche-individuelle.pdf');
    }

    public function etatHeures()
    {
        $enseignant = Auth::user()->enseignant;

        $idEnseignant = $enseignant->id;

        // Récupérer le service statutaire depuis les paramètres de calcul de l'année active
        $params = ParametreCalcul::anneeActive()->first();
        $serviceStatutaire = $params ? $params->service_statutaire : 10;

        $activites = ActivitePedagogique::whereHas('affectationCours', function ($query) use ($idEnseignant) {
            $query->where('id_enseignant', $idEnseignant);
        })
            ->where('statut', 'validee')
            ->with([
                'affectationCours.cours'
            ])
            ->get();

        $volumeRealise = $activites->sum('volume_horaire');

        $heuresComplementaires = max(0, $volumeRealise - $serviceStatutaire);

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
                ucfirst($activite->statut),
            ];
        })->toArray();

        // Ajouter le récapitulatif en bas
        $rows[] = ['', '', '', ''];
        $rows[] = ['Total réalisé', $volumeRealise . ' h', '', ''];
        $rows[] = ['Service statutaire', $serviceStatutaire . ' h', '', ''];
        $rows[] = ['Heures complémentaires', $heuresComplementaires . ' h', '', ''];

        $pdf = Pdf::loadView('exports.pdf-template', [
            'title' => 'État des Heures',
            'date' => now()->format('d/m/Y H:i'),
            'logo' => $this->logo(),
            'headers' => $headers,
            'rows' => $rows,
        ]);

        return $pdf->download('etat-heures.pdf');
    }
}
