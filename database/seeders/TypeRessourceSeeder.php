<?php

namespace Database\Seeders;

use App\Models\TypeRessource;
use Illuminate\Database\Seeder;

class TypeRessourceSeeder extends Seeder
{
    public function run(): void
    {
        TypeRessource::create([
            'libelle' => 'Texte',
        ]);

        TypeRessource::create([
            'libelle' => 'Video',
        ]);

        TypeRessource::create([
            'libelle' => 'Document',
        ]);

        TypeRessource::create([
            'libelle' => 'Quiz',
        ]);

        TypeRessource::create([
            'libelle' => 'Activité Interactive',
        ]);

        TypeRessource::create([
            'libelle' => 'Evaluation',
        ]);
    }
}
