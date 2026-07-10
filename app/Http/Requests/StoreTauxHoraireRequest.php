<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTauxHoraireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_grade' => 'required|exists:grades,id',
            'id_annee' => 'required|exists:annees_academiques,id',
            'montant' => 'required|numeric|min:0|max:9999999.99',
            'devise' => 'required|string|max:10|in:XOF,FCFA,EUR,USD',
            'date_application' => 'required|date|after_or_equal:today',
            'date_fin_application' => 'nullable|date|after:date_application',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérifier qu'un seul taux existe pour ce grade et cette année
            $existing = \App\Models\TauxHoraire::where('id_grade', $this->id_grade)
                ->where('id_annee', $this->id_annee)
                ->whereNull('date_fin_application')
                ->first();

            if ($existing) {
                $validator->errors()->add('id_grade', 'Un taux existe déjà pour ce grade et cette année académique.');
                $validator->errors()->add('id_annee', 'Un taux existe déjà pour ce grade et cette année académique.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_grade.required' => 'Le grade est requis.',
            'id_grade.exists' => 'Le grade sélectionné n\'existe pas.',
            'id_annee.required' => 'L\'année académique est requise.',
            'id_annee.exists' => 'L\'année académique sélectionnée n\'existe pas.',
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant ne peut être négatif.',
            'montant.max' => 'Le montant dépasse la valeur maximale autorisée.',
            'devise.required' => 'La devise est requise.',
            'devise.in' => 'La devise doit être XOF, FCFA, EUR ou USD.',
            'date_application.required' => 'La date d\'application est requise.',
            'date_application.date' => 'La date d\'application doit être une date valide.',
            'date_application.after_or_equal' => 'La date d\'application doit être aujourd\'hui ou dans le futur.',
            'date_fin_application.date' => 'La date de fin doit être une date valide.',
            'date_fin_application.after' => 'La date de fin doit être après la date d\'application.',
        ];
    }
}
