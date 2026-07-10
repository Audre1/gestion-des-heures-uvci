<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivitePedagogique;


class DocumentController extends Controller
{
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

    $pdf = Pdf::loadView('pdf.recapitulatif-activites', compact('activites'));

    return $pdf->download('recapitulatif-activites.pdf');
    
}


public function ficheIndividuelle()
{
    $enseignant = Auth::user()->enseignant;

    $idEnseignant = $enseignant->id;

    $activites = ActivitePedagogique::whereHas('affectationCours', function ($query) use ($idEnseignant) {
            $query->where('id_enseignant', $idEnseignant);
        })
        ->where('statut', 'realise')
        ->get();

    $volumeRealise = $activites->sum('volume_horaire');

    $serviceStatutaire = 10;

    $heuresComplementaires = max(0, $volumeRealise - $serviceStatutaire);

    $pdf = Pdf::loadView('pdf.fiche-individuelle', [
        'enseignant' => $enseignant,
        'volumeRealise' => $volumeRealise,
        'serviceStatutaire' => $serviceStatutaire,
        'heuresComplementaires' => $heuresComplementaires
    ]);

    return $pdf->download('fiche-individuelle.pdf');
}

public function etatHeures()
{
    $enseignant = Auth::user()->enseignant;

    $idEnseignant = $enseignant->id;

    $activites = ActivitePedagogique::whereHas('affectationCours', function ($query) use ($idEnseignant) {
            $query->where('id_enseignant', $idEnseignant);
        })
        ->where('statut', 'realise')
        ->with([
            'affectationCours.cours'
        ])
        ->get();


    $volumeRealise = $activites->sum('volume_horaire');

    $serviceStatutaire = 10;

    $heuresComplementaires = max(0, $volumeRealise - $serviceStatutaire);


    $pdf = Pdf::loadView('pdf.etat-heures', [
        'enseignant' => $enseignant,
        'activites' => $activites,
        'volumeRealise' => $volumeRealise,
        'serviceStatutaire' => $serviceStatutaire,
        'heuresComplementaires' => $heuresComplementaires
    ]);

    return $pdf->download('etat-heures.pdf');
}

}
