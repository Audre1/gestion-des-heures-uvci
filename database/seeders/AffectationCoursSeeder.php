<?php

namespace Database\Seeders;

use App\Models\AffectationCours;
use Illuminate\Database\Seeder;

class AffectationCoursSeeder extends Seeder
{
    public function run(): void
    {
        AffectationCours::create([
            'date_affectation' => '2025-09-15',
            'id_enseignant' => 1,
            'id_cours' => 1,
            'id_annee' => 1,
        ]);

        AffectationCours::create([
            'date_affectation' => '2025-09-15',
            'id_enseignant' => 1,
            'id_cours' => 2,
            'id_annee' => 1,
        ]);
    }
}