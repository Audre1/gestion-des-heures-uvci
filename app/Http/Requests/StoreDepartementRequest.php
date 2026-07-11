<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreDepartementRequest extends FormRequest
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
            'code_departement' => 'required|string|max:50|unique:departements,code_departement,NULL,id,deleted_at,NULL',
            'nom_departement' => 'required|string|max:200|unique:departements,nom_departement,NULL,id,deleted_at,NULL',
        ];
    }

    public function messages(): array
    {
        return [
            'code_departement.required' => 'Le code du département est requis.',
            'code_departement.max' => 'Le code ne doit pas dépasser 50 caractères.',
            'code_departement.unique' => 'Ce code de département existe déjà.',
            'nom_departement.required' => 'Le nom du département est requis.',
            'nom_departement.max' => 'Le nom ne doit pas dépasser 200 caractères.',
            'nom_departement.unique' => 'Ce nom de département existe déjà.',
        ];
    }
}
