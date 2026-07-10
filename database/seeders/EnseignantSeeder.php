<?php

namespace Database\Seeders;

use App\Models\Enseignant;
use Illuminate\Database\Seeder;

class EnseignantSeeder extends Seeder
{
    public function run(): void
    {
        Enseignant::create([
            'matricule' => 'ENS001',
            'nom' => 'Kouassi',
            'prenom' => 'Jean',
            'email' => 'jean.kouassi@test.com',
            'telephone' => '0700000000',
            'statut' => 'actif',
            'date_recrutement' => '2020-01-15',
            'id_grade' => 1,
            'id_departement' => 1,
            'id_utilisateur' => 1,
        ]);
    }
}