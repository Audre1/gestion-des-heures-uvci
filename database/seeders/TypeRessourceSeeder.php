<?php

namespace Database\Seeders;

use App\Models\TypeRessource;
use Illuminate\Database\Seeder;

class TypeRessourceSeeder extends Seeder
{
    public function run(): void
    {
        TypeRessource::create([
            'libelle' => 'Support de cours',
        ]);

        TypeRessource::create([
            'libelle' => 'Vidéo pédagogique',
        ]);

        TypeRessource::create([
            'libelle' => 'Document PDF',
        ]);
    }
}