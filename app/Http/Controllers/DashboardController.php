<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (function_exists('logActivite')) {
            logActivite('consultation', 'Accès au tableau de bord');
        }

        return view('dashboard');
    }
}
