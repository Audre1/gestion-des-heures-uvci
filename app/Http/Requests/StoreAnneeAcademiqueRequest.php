<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnneeAcademiqueRequest extends FormRequest
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
            'libelle' => 'required|string|max:100|unique:annees_academiques,libelle|regex:/^\d{4}-\d{4}$/',
            'date_debut' => 'required|date|before:date_fin',
            'date_fin' => 'required|date|after:date_debut',
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est requis.',
            'libelle.max' => 'Le libellé ne doit pas dépasser 100 caractères.',
            'libelle.unique' => 'Ce libellé existe déjà.',
            'libelle.regex' => 'Le libellé doit être au format "YYYY-YYYY" (ex: 2024-2025).',
            'date_debut.required' => 'La date de début est requise.',
            'date_debut.date' => 'Veuillez saisir une date valide.',
            'date_debut.before' => 'La date de début doit être antérieure à la date de fin.',
            'date_fin.required' => 'La date de fin est requise.',
            'date_fin.date' => 'Veuillez saisir une date valide.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}
