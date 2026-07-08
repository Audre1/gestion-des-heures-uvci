<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnneeAcademiqueRequest extends FormRequest
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
        $anneeId = $this->route('id');
        
        return [
            'libelle' => 'required|string|max:100|unique:annees_academiques,libelle,' . $anneeId,
            'date_debut' => 'required|date|before:date_fin',
            'date_fin' => 'required|date|after:date_debut',
            'statut' => 'required|in:a_venir,en_cours,cloturee',
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est requis.',
            'libelle.max' => 'Le libellé ne doit pas dépasser 100 caractères.',
            'libelle.unique' => 'Ce libellé existe déjà.',
            'date_debut.required' => 'La date de début est requise.',
            'date_debut.date' => 'Veuillez saisir une date valide.',
            'date_debut.before' => 'La date de début doit être antérieure à la date de fin.',
            'date_fin.required' => 'La date de fin est requise.',
            'date_fin.date' => 'Veuillez saisir une date valide.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}
