<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCoursRequest extends FormRequest
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
        $coursId = $this->route('id');

        return [
            'code_cours' => 'required|string|max:20|unique:cours,code_cours,' . $coursId . ',id,deleted_at,NULL|regex:/^[A-Z]{3}-\d{3}$/',
            'intitule' => 'required|string|max:255',
            'nombre_heures' => 'required|integer|min:1|max:200',
            'nombre_credits' => 'required|integer|min:0|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'code_cours.required' => 'Le code du cours est requis.',
            'code_cours.max' => 'Le code ne doit pas dépasser 20 caractères.',
            'code_cours.unique' => 'Ce code de cours existe déjà.',
            'code_cours.regex' => 'Le code doit être au format "XXX-000" (ex: INF-101).',
            'intitule.required' => 'L\'intitulé du cours est requis.',
            'intitule.max' => 'L\'intitulé ne doit pas dépasser 255 caractères.',
            'nombre_heures.required' => 'Le nombre d\'heures est requis.',
            'nombre_heures.integer' => 'Le nombre d\'heures doit être un entier.',
            'nombre_heures.min' => 'Le nombre d\'heures doit être au moins 1.',
            'nombre_heures.max' => 'Le nombre d\'heures ne peut pas dépasser 200.',
            'nombre_credits.required' => 'Le nombre de crédits est requis.',
            'nombre_credits.integer' => 'Le nombre de crédits doit être un entier.',
            'nombre_credits.min' => 'Le nombre de crédits ne peut pas être négatif.',
            'nombre_credits.max' => 'Le nombre de crédits ne peut pas dépasser 20.',
        ];
    }
}
