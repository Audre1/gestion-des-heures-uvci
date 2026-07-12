<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ActivitePedagogique;

class UpdateActivitePedagogiqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_activite' => 'required|in:creation,maj',
            'date_activite' => 'required|date',
            'statut' => 'nullable|in:en_cours,validee,rejetee',
            'coefficient' => 'nullable|numeric|min:0|max:100',
            'nb_sequences' => 'nullable|integer|min:1',
            'volume_horaire' => 'nullable|numeric|min:0',
            'id_affectation' => 'required|exists:affectations_cours,id',
            'id_ressource' => 'nullable|exists:ressources_pedagogiques,id',
            'id_niveau' => 'required|exists:niveaux_complexite,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $existing = ActivitePedagogique::where('id_affectation', $this->id_affectation)
                ->where('type_activite', $this->type_activite)
                ->where('id_niveau', $this->id_niveau)
                ->where('id', '!=', $this->route('id'))
                ->first();

            if ($existing) {
                $validator->errors()->add('id_affectation', 'Une activité avec les mêmes critères (affectation, type, niveau) existe déjà.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'type_activite.required' => 'Le type d\'activité est requis.',
            'type_activite.in' => 'Le type d\'activité doit être création ou mise à jour.',
            'date_activite.required' => 'La date de l\'activité est requise.',
            'date_activite.date' => 'La date de l\'activité doit être une date valide.',
            'statut.required' => 'Le statut est requis.',
            'statut.in' => 'Le statut doit être en cours, validée ou rejetée.',
            'coefficient.required' => 'Le coefficient est requis.',
            'coefficient.numeric' => 'Le coefficient doit être un nombre.',
            'coefficient.min' => 'Le coefficient doit être positif.',
            'coefficient.max' => 'Le coefficient ne peut pas dépasser 100.',
            'nb_sequences.required' => 'Le nombre de séquences est requis.',
            'nb_sequences.integer' => 'Le nombre de séquences doit être un entier.',
            'nb_sequences.min' => 'Le nombre de séquences doit être au moins 1.',
            'volume_horaire.required' => 'Le volume horaire est requis.',
            'volume_horaire.numeric' => 'Le volume horaire doit être un nombre.',
            'volume_horaire.min' => 'Le volume horaire doit être positif.',
            'id_affectation.required' => 'L\'affectation de cours est requise.',
            'id_affectation.exists' => 'L\'affectation de cours n\'existe pas.',
            'id_ressource.exists' => 'La ressource pédagogique n\'existe pas.',
            'id_niveau.required' => 'Le niveau de complexité est requis.',
            'id_niveau.exists' => 'Le niveau de complexité n\'existe pas.',
        ];
    }
}