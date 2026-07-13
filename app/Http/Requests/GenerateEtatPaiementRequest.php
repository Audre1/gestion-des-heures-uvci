<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateEtatPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_enseignant' => 'required|exists:enseignants,id',
            'id_annee' => 'required|exists:annees_academiques,id',
            'periode' => 'required|string|max:100|min:3',
        ];
    }

    public function messages(): array
    {
        return [
            'id_enseignant.required' => 'L\'enseignant est requis.',
            'id_enseignant.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            'id_annee.required' => 'L\'année académique est requise.',
            'id_annee.exists' => 'L\'année académique sélectionnée n\'existe pas.',
            'periode.required' => 'La période est requise.',
            'periode.string' => 'La période doit être une chaîne de caractères.',
            'periode.max' => 'La période ne peut pas dépasser 100 caractères.',
            'periode.min' => 'La période doit contenir au moins 3 caractères.',
        ];
    }
}
