<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEnseignant
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $role = Auth::user()->role;

        if (!$role || $role->code !== 'enseignant') {
            abort(403, 'Accès réservé aux enseignants.');
        }

        return $next($request);
    }
}