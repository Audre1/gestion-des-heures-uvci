<?php

namespace App\Http\Controllers;

class PedagogieController extends Controller
{
    public function enseignants()
    {
        return view('pedagogie.enseignants');
    }

    public function grades()
    {
        return view('pedagogie.grades');
    }

    public function departements()
    {
        return view('pedagogie.departements');
    }

    public function filieres()
    {
        return view('pedagogie.filieres');
    }

    public function cours()
    {
        return view('pedagogie.cours');
    }

    public function affectations()
    {
        return view('pedagogie.affectations');
    }

    public function sequences()
    {
        return view('pedagogie.sequences');
    }

    public function ressources()
    {
        return view('pedagogie.ressources');
    }

    public function typesRessources()
    {
        return view('pedagogie.types-ressources');
    }

    public function niveauxComplexite()
    {
        return view('pedagogie.niveaux');
    }

    public function activites()
    {
        return view('pedagogie.activites');
    }

    public function volumes()
    {
        return view('pedagogie.volumes');
    }

    public function complementaires()
    {
        return view('pedagogie.complementaires');
    }
}
