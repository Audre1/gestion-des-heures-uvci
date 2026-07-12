<?php

namespace Database\Seeders;

use App\Models\TypeRessource;
use Illuminate\Database\Seeder;

class TypeRessourceSeeder extends Seeder
{
    public function run(): void
    {
        TypeRessource::updateOrCreate([
            'libelle' => 'Texte',
        ]);

        TypeRessource::updateOrCreate([
            'libelle' => 'Video',
        ]);

        TypeRessource::updateOrCreate([
            'libelle' => 'Document',
        ]);

        TypeRessource::updateOrCreate([
            'libelle' => 'Quiz',
        ]);

        TypeRessource::updateOrCreate([
            'libelle' => 'Activité Interactive',
        ]);

        TypeRessource::updateOrCreate([
            'libelle' => 'Evaluation',
        ]);
    }
}
