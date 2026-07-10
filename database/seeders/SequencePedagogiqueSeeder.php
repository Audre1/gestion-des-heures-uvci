<?php

namespace Database\Seeders;

use App\Models\SequencePedagogique;
use Illuminate\Database\Seeder;

class SequencePedagogiqueSeeder extends Seeder
{
    public function run(): void
    {
        SequencePedagogique::create([
            'titre' => 'Introduction à l’algorithmique',
            'numero_ordre' => 1,
            'id_cours' => 1,
        ]);

        SequencePedagogique::create([
            'titre' => 'Modélisation des bases de données',
            'numero_ordre' => 2,
            'id_cours' => 2,
        ]);
    }
}