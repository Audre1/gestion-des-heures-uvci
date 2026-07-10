<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class CompteController extends Controller
{
    public function profil()
    {
        $enseignant = Auth::user()->enseignant;

        return view('compte.profil', compact('enseignant'));
    }
}