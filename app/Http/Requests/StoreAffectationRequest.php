<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAffectationRequest extends FormRequest
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
            'id_enseignant' => 'required|exists:enseignants,id',
            'id_cours' => 'required|exists:cours,id',
            'id_annee' => 'required|exists:annees_academiques,id',
            'date_affectation' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'id_enseignant.required' => 'L\'enseignant est requis.',
            'id_enseignant.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            'id_cours.required' => 'Le cours est requis.',
            'id_cours.exists' => 'Le cours sélectionné n\'existe pas.',
            'id_annee.required' => 'L\'année académique est requise.',
            'id_annee.exists' => 'L\'année académique sélectionnée n\'existe pas.',
            'date_affectation.required' => 'La date d\'affectation est requise.',
            'date_affectation.date' => 'Veuillez saisir une date valide.',
        ];
    }
}
