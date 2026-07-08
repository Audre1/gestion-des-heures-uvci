<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUtilisateurRequest extends FormRequest
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
        $userId = $this->route('id');
        
        return [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $userId . '|max:150',
            'telephone' => 'nullable|string|max:20',
            'mot_de_passe' => 'nullable|string|min:8|confirmed',
            'id_role' => 'required|exists:roles,id',
            'statut_compte' => 'required|in:actif,inactif,suspendu',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est requis.',
            'nom.max' => 'Le nom ne doit pas dépasser 100 caractères.',
            'prenom.required' => 'Le prénom est requis.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 100 caractères.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'email.max' => 'L\'email ne doit pas dépasser 150 caractères.',
            'telephone.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'id_role.required' => 'Le rôle est requis.',
            'id_role.exists' => 'Le rôle sélectionné n\'existe pas.',
            'statut_compte.required' => 'Le statut du compte est requis.',
            'statut_compte.in' => 'Le statut du compte doit être actif, inactif ou suspendu.',
        ];
    }
}
