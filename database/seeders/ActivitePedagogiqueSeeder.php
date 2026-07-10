<?php

namespace Database\Seeders;

use App\Models\ActivitePedagogique;
use Illuminate\Database\Seeder;

class ActivitePedagogiqueSeeder extends Seeder
{
    public function run(): void
    {
        ActivitePedagogique::create([
            'type_activite' => 'Cours magistral',
            'date_activite' => '2025-10-15',
            'statut' => 'realise',
            'coefficient' => 1,
            'nb_sequences' => 10,
            'volume_horaire' => 20,
            'id_affectation' => 1,
            'id_niveau' => 1,
        ]);

        ActivitePedagogique::create([
            'type_activite' => 'Travaux dirigés',
            'date_activite' => '2025-10-20',
            'statut' => 'planifie',
            'coefficient' => 1,
            'nb_sequences' => 5,
            'volume_horaire' => 10,
            'id_affectation' => 2,
            'id_niveau' => 1,
        ]);
    }
}