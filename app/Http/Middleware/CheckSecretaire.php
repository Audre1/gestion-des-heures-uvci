<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSecretaire
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $role = Auth::user()->role;

        if (!$role || $role->code !== 'secretaire') {
            abort(403, 'Accès réservé aux secrétaires.');
        }

        return $next($request);
    }
}