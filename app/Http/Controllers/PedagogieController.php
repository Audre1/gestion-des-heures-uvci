<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnseignantRequest;
use App\Models\Enseignant;
use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PedagogieController extends Controller
{
    public function enseignants()
    {
        $enseignants = Enseignant::with(['utilisateur', 'utilisateur.createdBy', 'grade', 'departement'])->get();

        return view('pedagogie.enseignants', compact('enseignants'));
    }

    public function storeEnseignant(StoreEnseignantRequest $request)
    {
        DB::transaction(function () use ($request) {
            // Récupérer le rôle enseignant
            $enseignantRole = Role::where('code', 'enseignant')->first();

            // Récupérer uniquement le premier prénom
            $premierPrenom = explode(' ', trim($request->prenom))[0];

            // Générer le login : premier_prenom.nom
            $login = strtolower($premierPrenom.'.'.$request->nom);

            // Remplacer les accents et caractères spéciaux
            $login = Str::slug($login, '.');

            // Vérifier si le login existe déjà et ajouter un suffixe si nécessaire
            $counter = 1;
            $originalLogin = $login;
            while (Utilisateur::where('login', $login)->exists()) {
                $login = $originalLogin.$counter;
                $counter++;
            }

            // Créer Utilisateur avec rôle enseignant
            $user = Utilisateur::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'login' => $login,
                'mot_de_passe' => Hash::make($request->mot_de_passe),
                'id_role' => $enseignantRole->id,
                'statut_compte' => 'actif',
                'created_by' => auth()->id(),
            ]);

            // Créer Enseignant
            Enseignant::create([
                'matricule' => $request->matricule,
                'statut' => $request->statut,
                'date_recrutement' => $request->date_recrutement,
                'id_grade' => $request->id_grade,
                'id_departement' => $request->id_departement,
                'id_utilisateur' => $user->id,
            ]);
        });

        return redirect()->route('enseignants.index')->with('success', 'Enseignant créé avec succès.');
    }

    public function grades()
    {
        return view('pedagogie.grades');
    }

    public function departements()
    {
        return view('pedagogie.departements');
    }

    public function filieres()
    {
        return view('pedagogie.filieres');
    }

    public function cours()
    {
        return view('pedagogie.cours');
    }

    public function affectations()
    {
        return view('pedagogie.affectations');
    }

    public function sequences()
    {
        return view('pedagogie.sequences');
    }

    public function ressources()
    {
        return view('pedagogie.ressources');
    }

    public function typesRessources()
    {
        return view('pedagogie.types-ressources');
    }

    public function niveauxComplexite()
    {
        return view('pedagogie.niveaux');
    }

    public function activites()
    {
        return view('pedagogie.activites');
    }

    public function volumes()
    {
        return view('pedagogie.volumes');
    }

    public function complementaires()
    {
        return view('pedagogie.complementaires');
    }
}
