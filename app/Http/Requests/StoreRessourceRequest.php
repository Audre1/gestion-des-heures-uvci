<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRessourceRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre' => 'required|string|max:255',
            'id_sequence' => 'required|exists:sequences_pedagogiques,id',
            'id_type' => 'required|exists:type_ressources,id',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est requis.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'id_sequence.required' => 'La séquence est requise.',
            'id_sequence.exists' => 'La séquence sélectionnée n\'existe pas.',
            'id_type.required' => 'Le type de ressource est requis.',
            'id_type.exists' => 'Le type de ressource sélectionné n\'existe pas.',
        ];
    }
}
