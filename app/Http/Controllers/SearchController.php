<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enseignant;
use App\Models\Cours;
use App\Models\Filiere;
use App\Models\Departement;
use App\Models\ActivitePedagogique;
use App\Models\RessourcePedagogique;
use App\Models\Utilisateur;
use App\Models\AnneeAcademique;
use App\Models\NiveauComplexite;
use App\Models\TauxHoraire;
use App\Models\Grade;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $userRole = Auth::user()->role->code;
        $results = [];

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        if ($userRole === 'admin') {
            // Recherche pour admin (Administration uniquement)
            $results['utilisateurs'] = Utilisateur::where('nom', 'like', "%{$query}%")
                ->orWhere('prenom', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($utilisateur) {
                    return [
                        'id' => $utilisateur->id,
                        'label' => $utilisateur->prenom . ' ' . $utilisateur->nom,
                        'subtitle' => $utilisateur->email,
                        'url' => route('utilisateurs.index') . '?highlight=utilisateur:' . $utilisateur->id,
                        'icon' => 'fa-user'
                    ];
                });

            $results['annees'] = AnneeAcademique::where('libelle', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($annee) {
                    return [
                        'id' => $annee->id,
                        'label' => $annee->libelle,
                        'subtitle' => ucfirst(str_replace('_', ' ', $annee->statut)),
                        'url' => route('annees.index') . '?highlight=annee:' . $annee->id,
                        'icon' => 'fa-calendar-days'
                    ];
                });

            $results['niveaux'] = NiveauComplexite::where('libelle', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($niveau) {
                    return [
                        'id' => $niveau->id,
                        'label' => $niveau->libelle,
                        'subtitle' => 'Coefficient: ' . $niveau->coefficient,
                        'url' => route('niveaux.index') . '?highlight=niveau:' . $niveau->id,
                        'icon' => 'fa-signal'
                    ];
                });

            $results['taux'] = TauxHoraire::whereHas('grade', function ($q) use ($query) {
                $q->where('libelle', 'like', "%{$query}%");
            })
                ->with('grade')
                ->limit(5)
                ->get()
                ->map(function ($taux) {
                    return [
                        'id' => $taux->id,
                        'label' => $taux->grade->libelle,
                        'subtitle' => $taux->montant . ' FCFA/h',
                        'url' => route('taux.index') . '?highlight=taux:' . $taux->id,
                        'icon' => 'fa-money-bill-wave'
                    ];
                });
        } elseif ($userRole === 'secretaire') {
            // Recherche pour secretaire (Gestion pédagogique)
            $results['enseignants'] = Enseignant::whereHas('utilisateur', function ($q) use ($query) {
                $q->where('nom', 'like', "%{$query}%")
                    ->orWhere('prenom', 'like', "%{$query}%");
            })
                ->with('utilisateur', 'grade', 'departement')
                ->limit(5)
                ->get()
                ->map(function ($enseignant) {
                    return [
                        'id' => $enseignant->id,
                        'label' => $enseignant->utilisateur->prenom . ' ' . $enseignant->utilisateur->nom,
                        'subtitle' => $enseignant->grade->libelle ?? 'Non défini',
                        'url' => route('enseignants.index') . '?highlight=enseignant:' . $enseignant->id,
                        'icon' => 'fa-chalkboard-user'
                    ];
                });

            $results['cours'] = Cours::where('code_cours', 'like', "%{$query}%")
                ->orWhere('intitule', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($cours) {
                    return [
                        'id' => $cours->id,
                        'label' => $cours->code_cours,
                        'subtitle' => $cours->intitule,
                        'url' => route('cours.index') . '?highlight=cours:' . $cours->id,
                        'icon' => 'fa-book'
                    ];
                });

            $results['filieres'] = Filiere::where('nom_filiere', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($filiere) {
                    return [
                        'id' => $filiere->id,
                        'label' => $filiere->nom_filiere,
                        'subtitle' => $filiere->departement->nom_departement ?? 'Non défini',
                        'url' => route('filieres.index') . '?highlight=filiere:' . $filiere->id,
                        'icon' => 'fa-sitemap'
                    ];
                });

            $results['departements'] = Departement::where('nom_departement', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($departement) {
                    return [
                        'id' => $departement->id,
                        'label' => $departement->nom_departement,
                        'subtitle' => 'Département',
                        'url' => route('departements.index') . '?highlight=departement:' . $departement->id,
                        'icon' => 'fa-building-columns'
                    ];
                });

            $results['grades'] = Grade::where('libelle', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($grade) {
                    return [
                        'id' => $grade->id,
                        'label' => $grade->libelle,
                        'subtitle' => 'Grade',
                        'url' => route('grades.index') . '?highlight=grade:' . $grade->id,
                        'icon' => 'fa-ranking-star'
                    ];
                });
        } elseif ($userRole === 'enseignant') {
            // Recherche pour enseignant (ses propres données)
            $idEnseignant = Auth::user()->enseignant->id;

            // Recherche des activités pédagogiques
            $activites = ActivitePedagogique::whereHas('affectationCours', function ($q) use ($idEnseignant) {
                $q->where('id_enseignant', $idEnseignant);
            })
                ->whereHas('affectationCours.cours', function ($q) use ($query) {
                    $q->where(function ($subQuery) use ($query) {
                        $subQuery->where('code_cours', 'like', "%{$query}%")
                            ->orWhere('intitule', 'like', "%{$query}%");
                    });
                })
                ->with(['affectationCours.cours'])
                ->limit(5)
                ->get();

            if ($activites->isNotEmpty()) {
                $results['activites'] = $activites->map(function ($activite) {
                    return [
                        'id' => $activite->id,
                        'label' => $activite->affectationCours->cours->code_cours,
                        'subtitle' => $activite->date_activite->format('d/m/Y') . ' - ' . $activite->volume_horaire . 'h',
                        'url' => route('espace.activites') . '?highlight=activite:' . $activite->id,
                        'icon' => 'fa-folder-open'
                    ];
                });
            }

            // Recherche des ressources pédagogiques
            $ressources = RessourcePedagogique::whereHas('sequence.cours.affectationsCours', function ($q) use ($idEnseignant) {
                $q->where('id_enseignant', $idEnseignant);
            })
                ->where('titre', 'like', "%{$query}%")
                ->with('sequence.cours')
                ->limit(5)
                ->get();

            if ($ressources->isNotEmpty()) {
                $results['ressources'] = $ressources->map(function ($ressource) {
                    return [
                        'id' => $ressource->id,
                        'label' => $ressource->titre,
                        'subtitle' => $ressource->sequence->cours->code_cours ?? 'Non défini',
                        'url' => route('espace.ressources') . '?highlight=ressource:' . $ressource->id,
                        'icon' => 'fa-book-open'
                    ];
                });
            }
        }

        return response()->json(['results' => $results]);
    }
}
