<?php

namespace Database\Seeders;

use App\Models\NiveauComplexite;
use Illuminate\Database\Seeder;

class NiveauComplexiteSeeder extends Seeder
{
    public function run(): void
    {
        $niveaux = [
            ['libelle' => 'Niveau 1', 'coefficient' => 0.4],
            ['libelle' => 'Niveau 2', 'coefficient' => 0.75],
            ['libelle' => 'Niveau 3', 'coefficient' => 1.5],
            ['libelle' => 'Niveau 4', 'coefficient' => 2.0],
        ];

        foreach ($niveaux as $n) {
            NiveauComplexite::create($n);
        }
    }
}