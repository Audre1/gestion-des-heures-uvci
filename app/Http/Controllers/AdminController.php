<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function utilisateurs()
    {
        return view('admin.utilisateurs');
    }

    public function annees()
    {
        return view('admin.annees');
    }

    public function parametres()
    {
        return view('admin.parametres');
    }

    public function taux()
    {
        return view('admin.taux');
    }

    public function journaux()
    {
        return view('admin.journaux');
    }

    public function sauvegardes()
    {
        return view('admin.sauvegardes');
    }
}
