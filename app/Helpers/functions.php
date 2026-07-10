<?php

use App\Models\JournalActivite;
use Illuminate\Support\Facades\Auth;

if (!function_exists('logActivite')) {
    /**
     * Enregistre une activité dans le journal d'activités.
     *
     * @param string $action L'action effectuée (ex: 'create', 'update', 'delete')
     * @param string $description Description de l'activité
     * @param mixed $model Le modèle concerné (optionnel)
     * @return JournalActivite|null
     */
    function logActivite(string $action, string $description, $model = null): ?JournalActivite
    {
        $utilisateurId = Auth::id();
        $modelType = null;
        $modelId = null;

        if ($model) {
            $modelType = get_class($model);
            $modelId = $model->id;
        }

        return JournalActivite::create([
            'utilisateur_id' => $utilisateurId,
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
