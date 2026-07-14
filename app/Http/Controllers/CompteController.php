<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompteController extends Controller
{
    public function profil()
    {
        $utilisateur = Auth::user();

        if (!$utilisateur) {
            return redirect()->route('login');
        }

        $role = $utilisateur->role;

        // Données de base commune à tous les rôles
        $data = [
            'utilisateur' => $utilisateur,
            'role' => $role,
            'enseignant' => null,
        ];

        // Si c'est un enseignant, on charge ses infos spécifiques
        if ($utilisateur->enseignant()->exists()) {
            $enseignant = $utilisateur->enseignant;
            $enseignant->load(['grade', 'departement']);
            $data['enseignant'] = $enseignant;
        }

        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation du profil utilisateur');
        }

        return view('compte.profil', $data);
    }

    public function updateProfil(Request $request)
    {
        $utilisateur = Auth::user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|' . Rule::unique('users', 'email')->ignore($utilisateur->id),
            'telephone' => 'nullable|string|max:20',
        ], [
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez fournir un email valide.',
            'email.unique' => 'Cet email est déjà utilisé par un autre compte.',
        ]);

        $utilisateur->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Mise à jour des informations personnelles');
        }

        return redirect()->route('profil.index')
            ->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Veuillez saisir votre mot de passe actuel.',
            'password.required' => 'Le nouveau mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $utilisateur = Auth::user();

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $utilisateur->mot_de_passe)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        // Mettre à jour le mot de passe
        $utilisateur->update([
            'mot_de_passe' => Hash::make($request->password),
        ]);

        if (function_exists('logActivite')) {
            logActivite('modification', 'Changement du mot de passe');
        }

        return redirect()->route('profil.index')
            ->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    }
}