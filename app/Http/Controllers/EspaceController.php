<?php

namespace App\Http\Controllers;

class EspaceController extends Controller
{
    public function activites()
    {
        return view('espace.activites');
    }

    public function volume()
    {
        return view('espace.volume');
    }

    public function complementaires()
    {
        return view('espace.complementaires');
    }

    public function ressources()
    {
        return view('espace.ressources');
    }

    public function documents()
    {
        return view('espace.documents');
    }
}
