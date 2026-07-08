<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnseignantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Champs Utilisateur
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:150',
            'telephone' => 'nullable|string|max:20',
            'mot_de_passe' => 'required|string|min:8|confirmed',
            // Champs Enseignant
            'matricule' => 'required|string|unique:enseignants,matricule|max:50',
            'statut' => 'required|in:actif,inactif,retraite',
            'date_recrutement' => 'required|date',
            'id_grade' => 'required|exists:grades,id',
            'id_departement' => 'required|exists:departements,id',
        ];
    }

    public function messages(): array
    {
        return [
            // Messages Utilisateur
            'nom.required' => 'Le nom est requis.',
            'nom.max' => 'Le nom ne doit pas dépasser 100 caractères.',
            'prenom.required' => 'Le prénom est requis.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 100 caractères.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'email.max' => 'L\'email ne doit pas dépasser 150 caractères.',
            'telephone.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
            'mot_de_passe.required' => 'Le mot de passe est requis.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            // Messages Enseignant
            'matricule.required' => 'Le matricule est requis.',
            'matricule.unique' => 'Ce matricule est déjà utilisé.',
            'matricule.max' => 'Le matricule ne doit pas dépasser 50 caractères.',
            'statut.required' => 'Le statut est requis.',
            'statut.in' => 'Le statut doit être actif, inactif ou retraite.',
            'date_recrutement.required' => 'La date de recrutement est requise.',
            'date_recrutement.date' => 'Veuillez saisir une date valide.',
            'id_grade.required' => 'Le grade est requis.',
            'id_grade.exists' => 'Le grade sélectionné n\'existe pas.',
            'id_departement.required' => 'Le département est requis.',
            'id_departement.exists' => 'Le département sélectionné n\'existe pas.',
        ];
    }
}
