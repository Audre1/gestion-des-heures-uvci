<?php

namespace App\Http\Middleware;

use App\Services\BackupService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AutoBackupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Exécuter la requête normalement
        $response = $next($request);

        // Vérifier si l'utilisateur est connecté et est admin
        if (Auth::check() && Auth::user()->role->code === 'admin') {
            try {
                $backupService = new BackupService();

                // Récupérer le délai de sauvegarde depuis les paramètres
                $delayHours = $backupService->getBackupDelay();

                // Vérifier si une sauvegarde automatique récente existe
                if (!$backupService->hasRecentAutoBackup($delayHours)) {
                    // Créer une sauvegarde automatique en arrière-plan
                    // On utilise ignore_user_abort pour que le processus continue même si l'utilisateur ferme la page
                    ignore_user_abort(true);
                    set_time_limit(300); // 5 minutes max

                    Log::info('Déclenchement de la sauvegarde automatique après connexion admin', [
                        'user' => Auth::user()->login,
                        'delay_hours' => $delayHours
                    ]);

                    $backupService->autoBackup();

                    Log::info('Sauvegarde automatique terminée après connexion admin');
                }
            } catch (\Exception $e) {
                // Ne pas bloquer la connexion en cas d'erreur de sauvegarde
                Log::error('Erreur lors de la sauvegarde automatique après connexion admin', [
                    'error' => $e->getMessage(),
                    'user' => Auth::check() ? Auth::user()->login : 'unknown'
                ]);
            }
        }

        return $response;
    }
}
