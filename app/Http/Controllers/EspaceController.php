<?php

namespace App\Http\Controllers;

use App\Models\ActivitePedagogique;
use App\Models\Enseignant;
use App\Models\TauxHoraire;
use App\Models\RessourcePedagogique;
use Illuminate\Support\Facades\Auth;

class EspaceController extends Controller
{
    public function activites()
    {
        // Temporaire : enseignant n°1
        // Cette ligne sera remplacée lorsque
        // l'authentification sera terminée.
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
            ->where('statut', 'realise')
            ->get();

        $volumeRealise = $activites->sum('volume_horaire');

        $nombreActivites = $activites->count();

        $serviceStatutaire = 10;

        $heuresComplementaires = max(0, $volumeRealise - $serviceStatutaire);

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



        $enseignant = Enseignant::find($idEnseignant);

        $tauxHoraire = TauxHoraire::where('id_grade', $enseignant->id_grade)
            ->where('id_annee', 1)
            ->first();

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
            'tauxHoraire',
            'chargeGlobale'
        ));
    }

    public function complementaires()
    {
        $idEnseignant = Auth::user()->enseignant->id;

        $serviceStatutaire = 10; // valeur temporaire pour test

        $activites = ActivitePedagogique::query()
            ->whereHas('affectationCours', function ($query) use ($idEnseignant) {
                $query->where('id_enseignant', $idEnseignant);
            })
            ->where('statut', 'realise')
            ->with([
                'affectationCours.cours'
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


        $heuresComplementaires = max(0, $volumeRealise - $serviceStatutaire);


        $tauxHoraire = TauxHoraire::where('id_grade', 1)
            ->where('id_annee', 1)
            ->first();
        $montantEstime = $heuresComplementaires * $tauxHoraire->montant;

        return view('espace.complementaires', compact(
            'heuresComplementaires',
            'tauxHoraire',
            'montantEstime',
            'activites'
        ));
    }

    public function ressources()
    {
        $ressources = RessourcePedagogique::with([
            'sequence.cours',
            'typeRessource'
        ])
            ->orderByDesc('date_creation')
            ->get();

        return view('espace.ressources', compact('ressources'));
    }

    public function documents()
    {
        return view('espace.documents');
    }
}
