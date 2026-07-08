<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnneeAcademiqueRequest;
use App\Http\Requests\StoreUtilisateurRequest;
use App\Http\Requests\UpdateAnneeAcademiqueRequest;
use App\Http\Requests\UpdateUtilisateurRequest;
use App\Models\AnneeAcademique;
use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Page - Liste des utilisateurs
    public function utilisateurs()
    {
        $utilisateurs = Utilisateur::with(['role', 'createdBy'])->get();
        $roles = Role::where('code', '!=', 'enseignant')->get();

        return view('admin.utilisateurs', [
            'utilisateurs' => $utilisateurs,
            'roles' => $roles,
        ]);
    }

    public function store(StoreUtilisateurRequest $request)
    {
        // Récupérer uniquement le premier prénom
        $premierPrenom = explode(' ', trim($request->prenom))[0];

        // Générer le login : premier_prenom.nom
        $login = strtolower($premierPrenom.'.'.$request->nom);

        // Remplacer les accents et caractères spéciaux
        $login = Str::slug($login, '.');

        // Vérifier si le login existe déjà et ajouter un suffixe
        $counter = 1;
        $originalLogin = $login;

        while (Utilisateur::where('login', $login)->exists()) {
            $login = $originalLogin.$counter;
            $counter++;
        }

        Utilisateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'login' => $login,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'id_role' => $request->id_role,
            'statut_compte' => $request->statut_compte,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès. Login : '.$login);
    }

    public function update(UpdateUtilisateurRequest $request, $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);

        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'id_role' => $request->id_role,
            'statut_compte' => $request->statut_compte,
        ];

        // Mettre à jour le mot de passe uniquement si fourni
        if ($request->filled('mot_de_passe')) {
            $data['mot_de_passe'] = Hash::make($request->mot_de_passe);
        }

        $utilisateur->update($data);

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy($id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->delete();

        return redirect()
            ->route('utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    // Page - Liste des années académiques
    public function annees()
    {
        $annees = AnneeAcademique::orderByRaw("CASE WHEN statut = 'en_cours' THEN 0 ELSE 1 END")
            ->orderBy('date_debut', 'desc')
            ->get();
        return view('admin.annees', compact('annees'));
    }

    public function storeAnnee(StoreAnneeAcademiqueRequest $request)
    {
        AnneeAcademique::create([
            'libelle' => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => 'a_venir',
        ]);

        return redirect()
            ->route('annees.index')
            ->with('success', 'Année académique créée avec succès.');
    }

    public function updateAnnee(UpdateAnneeAcademiqueRequest $request, $id)
    {
        $annee = AnneeAcademique::findOrFail($id);
        
        // Si on passe l'année en cours, désactiver toutes les autres
        if ($request->statut === 'en_cours') {
            AnneeAcademique::where('statut', 'en_cours')->where('id', '!=', $id)->update(['statut' => 'cloturee']);
        }
        
        $annee->update([
            'libelle' => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->statut,
        ]);

        return redirect()
            ->route('annees.index')
            ->with('success', 'Année académique modifiée avec succès.');
    }

    public function destroyAnnee($id)
    {
        $annee = AnneeAcademique::findOrFail($id);
        
        // Empêcher la suppression de l'année en cours
        if ($annee->statut === 'en_cours') {
            return redirect()
                ->route('annees.index')
                ->with('error', 'Impossible de supprimer l\'année académique en cours.');
        }
        
        $annee->delete();

        return redirect()
            ->route('annees.index')
            ->with('success', 'Année académique supprimée avec succès.');
    }

    public function activateAnnee($id)
    {
        $annee = AnneeAcademique::findOrFail($id);
        
        // Désactiver toutes les autres années
        AnneeAcademique::where('statut', 'en_cours')->update(['statut' => 'cloturee']);
        
        // Activer l'année sélectionnée
        $annee->update(['statut' => 'en_cours']);

        return redirect()
            ->route('annees.index')
            ->with('success', 'Année académique activée avec succès.');
    }

    public function parametres()
    {
        return view('admin.parametres');
    }

    public function taux()
    {
        return view('admin.taux');
    }

    public function journaux()
    {
        return view('admin.journaux');
    }

    public function sauvegardes()
    {
        return view('admin.sauvegardes');
    }
}
