<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur a un des rôles spécifiés.
     * Usage : ->middleware(['auth', 'role:admin,secretaire'])
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        if (!$userRole || !in_array($userRole->code, $roles)) {
            $roleLabels = array_map(fn($r) => match ($r) {
                'admin'      => 'administrateur',
                'secretaire' => 'secrétaire',
                'enseignant' => 'enseignant',
                default      => $r,
            }, $roles);

            $message = 'Accès réservé aux ' . implode(' ou ', $roleLabels) . '.';
            abort(403, $message);
        }

        return $next($request);
    }
}