<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParametreCalculRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Règles générales
            'heures_par_credit'     => 'required|integer|min:1|max:100',
            'sequences_par_credit'  => 'required|integer|min:1|max:200|gt:heures_par_credit',
            'service_statutaire'    => 'required|integer|min:1|max:500',
            'reduction_mise_a_jour' => 'required|integer|min:0|max:99',

            // Règles de sauvegarde automatique
            'sauvegarde_auto_delai' => 'required|integer|min:1|max:168',
            'sauvegarde_auto_rotation' => 'required|integer|min:1|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'heures_par_credit.required'     => 'Le nombre d\'heures par crédit est requis.',
            'heures_par_credit.integer'      => 'Le nombre d\'heures par crédit doit être un entier.',
            'heures_par_credit.min'          => 'Le nombre d\'heures par crédit doit être au moins 1.',
            'heures_par_credit.max'          => 'Le nombre d\'heures par crédit ne peut excéder 100.',

            'sequences_par_credit.required'  => 'Le nombre de séquences par crédit est requis.',
            'sequences_par_credit.integer'   => 'Le nombre de séquences par crédit doit être un entier.',
            'sequences_par_credit.min'       => 'Le nombre de séquences par crédit doit être au moins 1.',
            'sequences_par_credit.max'       => 'Le nombre de séquences par crédit ne peut excéder 200.',
            'sequences_par_credit.gt'        => 'Le nombre de séquences par crédit doit être supérieur au nombre d\'heures par crédit.',

            'service_statutaire.required'    => 'Le service statutaire est requis.',
            'service_statutaire.integer'     => 'Le service statutaire doit être un entier.',
            'service_statutaire.min'         => 'Le service statutaire doit être au moins 1.',
            'service_statutaire.max'         => 'Le service statutaire ne peut excéder 500.',

            'reduction_mise_a_jour.required' => 'La réduction mise à jour est requise.',
            'reduction_mise_a_jour.integer'  => 'La réduction mise à jour doit être un entier.',
            'reduction_mise_a_jour.min'      => 'La réduction mise à jour ne peut être négative.',
            'reduction_mise_a_jour.max'      => 'La réduction mise à jour ne peut excéder 100%.',

            'sauvegarde_auto_delai.required' => 'Le délai de sauvegarde est requis.',
            'sauvegarde_auto_delai.integer'  => 'Le délai de sauvegarde doit être un entier.',
            'sauvegarde_auto_delai.min'      => 'Le délai de sauvegarde doit être au moins 1 heure.',
            'sauvegarde_auto_delai.max'      => 'Le délai de sauvegarde ne peut excéder 168 heures (7 jours).',

            'sauvegarde_auto_rotation.required' => 'La rotation de sauvegarde est requise.',
            'sauvegarde_auto_rotation.integer'  => 'La rotation de sauvegarde doit être un entier.',
            'sauvegarde_auto_rotation.min'      => 'La rotation de sauvegarde doit être au moins 1.',
            'sauvegarde_auto_rotation.max'      => 'La rotation de sauvegarde ne peut excéder 30.',

            '*.numeric'                      => 'Ce champ doit être un nombre.',
            '*.min'                          => 'Ce champ doit être positif.',
            '*.max'                          => 'Ce champ dépasse la valeur maximale autorisée.',
        ];
    }
}
