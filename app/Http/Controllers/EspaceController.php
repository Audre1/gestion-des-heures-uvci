<?php

namespace App\Http\Controllers;

use App\Models\ActivitePedagogique;
use App\Models\Enseignant;
use App\Models\RessourcePedagogique;
use App\Models\ParametreCalcul;
use App\Models\AnneeAcademique;
use Illuminate\Support\Facades\Auth;

class EspaceController extends Controller
{
    public function activites()
    {

        // L'enseignant connecté
        $idEnseignant = Auth::user()->enseignant->id;

        $activites = ActivitePedagogique::query()
            ->whereHas('affectationCours', function ($query) use ($idEnseignant) {
                $query->where('id_enseignant', $idEnseignant);
            })
            ->with([
                'affectationCours.cours',
                'affectationCours.anneeAcademique',
                'niveauComplexite',
                'ressourcePedagogique',
            ])
            ->orderByDesc('date_activite')
            ->get();

        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation des activités pédagogiques par l\'enseignant');
        }

        return view('espace.activites', compact('activites'));
    }

    public function volume()
    {
        $idEnseignant = Auth::user()->enseignant->id;

        $activites = ActivitePedagogique::whereHas('affectationCours', function ($query) use ($idEnseignant) {
            $query->where('id_enseignant', $idEnseignant);
        })
            ->where('statut', 'validee')
            ->get();

        $volumeRealise = $activites->sum('volume_horaire');

        $nombreActivites = $activites->count();

        // Récupérer l'enseignant et son statut
        $enseignant = Enseignant::with('grade')->find($idEnseignant);
        $statutEnseignant = $enseignant->statut; // 'Permanent' ou 'Vacataire'

        // Récupérer le service statutaire depuis les paramètres de calcul de l'année active
        // Uniquement pour les permanents
        $params = ParametreCalcul::anneeActive()->first();
        $serviceStatutaire = ($statutEnseignant === 'Permanent')
            ? ($params ? $params->service_statutaire : 192)
            : 0;

        $heuresComplementaires = ($statutEnseignant === 'Permanent')
            ? max(0, $volumeRealise - $serviceStatutaire)
            : $volumeRealise; // Pour les vacataires, tout est complémentaire

        $evolutionMensuelle = collect([
            'Jan' => 0,
            'Fév' => 0,
            'Mar' => 0,
            'Avr' => 0,
            'Mai' => 0,
            'Juin' => 0,
            'Juil' => 0,
            'Août' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Déc' => 0,
        ]);

        $activites->groupBy(function ($activite) {
            return $activite->date_activite->format('M');
        })->each(function ($activitesDuMois, $mois) use ($evolutionMensuelle) {
            $evolutionMensuelle[$mois] = $activitesDuMois->sum('volume_horaire');
        });

        // Formater les données pour Chart.js
        $chartLabels = array_keys($evolutionMensuelle->toArray());
        $chartData = array_values($evolutionMensuelle->toArray());

        // Récupérer le taux horaire en utilisant la méthode du modèle
        $anneeActive = AnneeAcademique::where('statut', 'en_cours')->first();
        $anneeId = $anneeActive ? $anneeActive->id : null;
        $tauxHoraireMontant = $enseignant->getTauxHoraire($anneeId);

        $chargeGlobale = $serviceStatutaire > 0
            ? round(($volumeRealise / $serviceStatutaire) * 100)
            : 0;

        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation du volume horaire par l\'enseignant');
        }

        return view('espace.volume', compact(
            'volumeRealise',
            'nombreActivites',
            'serviceStatutaire',
            'heuresComplementaires',
            'evolutionMensuelle',
            'enseignant',
            'tauxHoraireMontant',
            'chargeGlobale',
            'anneeActive',
            'chartLabels',
            'chartData'
        ));
    }

    public function complementaires()
    {
        $idEnseignant = Auth::user()->enseignant->id;

        // Récupérer l'enseignant et son statut
        $enseignant = Enseignant::with('grade')->find($idEnseignant);
        $statutEnseignant = $enseignant->statut; // 'Permanent' ou 'Vacataire'

        // Récupérer le service statutaire depuis les paramètres de calcul de l'année active
        // Uniquement pour les permanents
        $params = ParametreCalcul::anneeActive()->first();
        $serviceStatutaire = ($statutEnseignant === 'Permanent')
            ? ($params ? $params->service_statutaire : 192)
            : 0; // Pour les vacataires, pas de service statutaire

        $activites = ActivitePedagogique::query()
            ->whereHas('affectationCours', function ($query) use ($idEnseignant) {
                $query->where('id_enseignant', $idEnseignant);
            })
            ->where('statut', 'validee')
            ->with([
                'affectationCours.cours',
                'affectationCours.anneeAcademique',
                'niveauComplexite'
            ])
            ->get();

        $heuresRestantes = $serviceStatutaire;

        $activites = $activites->map(function ($activite) use (&$heuresRestantes) {

            if ($heuresRestantes >= $activite->volume_horaire) {
                $activite->heures_complementaires = 0;
                $heuresRestantes -= $activite->volume_horaire;
            } else {
                $activite->heures_complementaires = $activite->volume_horaire - $heuresRestantes;
                $heuresRestantes = 0;
            }

            return $activite;
        });

        $volumeRealise = $activites->sum('volume_horaire');

        // Calcul des heures complémentaires selon le statut
        $heuresComplementaires = ($statutEnseignant === 'Permanent')
            ? max(0, $volumeRealise - $serviceStatutaire)
            : $volumeRealise; // Pour les vacataires, tout est complémentaire

        // Récupérer le taux horaire en utilisant la méthode du modèle
        $anneeActive = AnneeAcademique::where('statut', 'en_cours')->first();
        $anneeId = $anneeActive ? $anneeActive->id : null;
        $tauxHoraireMontant = $enseignant->getTauxHoraire($anneeId);
        $montantEstime = $heuresComplementaires * $tauxHoraireMontant;

        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation des heures complémentaires par l\'enseignant');
        }

        return view('espace.complementaires', compact(
            'heuresComplementaires',
            'tauxHoraireMontant',
            'montantEstime',
            'activites'
        ));
    }

    public function ressources()
    {
        $idEnseignant = Auth::user()->enseignant->id;

        $ressources = RessourcePedagogique::whereHas('activitesPedagogiques.affectationCours', function ($query) use ($idEnseignant) {
            $query->where('id_enseignant', $idEnseignant);
        })
            ->with([
                'sequence.cours',
                'typeRessource'
            ])
            ->orderByDesc('date_creation')
            ->get();

        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation des ressources pédagogiques par l\'enseignant');
        }

        return view('espace.ressources', compact('ressources'));
    }

    public function documents()
    {
        // Récupérer toutes les années académiques
        $annees = AnneeAcademique::where('statut', '!=', 'a_venir')->orderBy('libelle', 'desc')->get();
        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation des documents par l\'enseignant');
        }

        return view('espace.documents', compact('annees'));
    }
}
