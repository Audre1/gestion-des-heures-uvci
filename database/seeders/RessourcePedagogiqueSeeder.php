<?php

namespace Database\Seeders;

use App\Models\RessourcePedagogique;
use Illuminate\Database\Seeder;

class RessourcePedagogiqueSeeder extends Seeder
{
    public function run(): void
    {
        RessourcePedagogique::create([
            'titre' => 'Support de cours Algorithmique',
            'id_sequence' => 1,
            'id_type' => 1,
        ]);

        RessourcePedagogique::create([
            'titre' => 'Vidéo introduction Bases de données',
            'id_sequence' => 2,
            'id_type' => 2,
        ]);
    }
}