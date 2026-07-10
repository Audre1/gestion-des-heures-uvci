<?php

namespace Database\Seeders;

use App\Models\Cours;
use Illuminate\Database\Seeder;

class CoursSeeder extends Seeder
{
    public function run(): void
    {
        Cours::create([
            'code_cours' => 'INF101',
            'intitule' => 'Algorithmique',
            'nombre_heures' => 40,
            'nombre_credits' => 4,
            'semestre' => 'S1',
            'niveau' => 'L1',
        ]);

        Cours::create([
            'code_cours' => 'INF205',
            'intitule' => 'Bases de données',
            'nombre_heures' => 30,
            'nombre_credits' => 3,
            'semestre' => 'S2',
            'niveau' => 'L2',
        ]);
    }
}