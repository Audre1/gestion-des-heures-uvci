<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class CompteController extends Controller
{
    public function profil()
    {
        $enseignant = Auth::user()->enseignant;

        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation du profil utilisateur');
        }

        return view('compte.profil', compact('enseignant'));
    }
}
