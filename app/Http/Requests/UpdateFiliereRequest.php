<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateFiliereRequest extends FormRequest
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
        $filiereId = $this->route('id');

        return [
            'code_filiere' => 'required|string|max:50|unique:filieres,code_filiere,' . $filiereId . ',id,deleted_at,NULL',
            'nom_filiere' => 'required|string|max:200',
            'id_departement' => 'required|exists:departements,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code_filiere.required' => 'Le code de la filière est requis.',
            'code_filiere.max' => 'Le code ne doit pas dépasser 50 caractères.',
            'code_filiere.unique' => 'Ce code de filière existe déjà.',
            'nom_filiere.required' => 'Le nom de la filière est requis.',
            'nom_filiere.max' => 'Le nom ne doit pas dépasser 200 caractères.',
            'id_departement.required' => 'Le département est requis.',
            'id_departement.exists' => 'Le département sélectionné n\'existe pas.',
        ];
    }
}
