<?php

namespace App\Http\Controllers;

class RapportController extends Controller
{
    public function index()
    {
        if (function_exists('logActivite')) {
            logActivite('consultation', 'Consultation de la page des rapports');
        }

        return view('rapports.index');
    }
}
