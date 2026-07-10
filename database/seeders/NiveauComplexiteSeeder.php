<?php

namespace Database\Seeders;

use App\Models\NiveauComplexite;
use Illuminate\Database\Seeder;

class NiveauComplexiteSeeder extends Seeder
{
    public function run(): void
    {
        NiveauComplexite::create([
            'libelle' => 'Niveau intermédiaire',
        ]);
    }
}