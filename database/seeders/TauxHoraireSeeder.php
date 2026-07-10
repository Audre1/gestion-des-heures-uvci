<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TauxHoraire;

class TauxHoraireSeeder extends Seeder
{
    public function run(): void
    {
        TauxHoraire::create([
            'montant' => 5000,
            'devise' => 'FCFA',
            'date_application' => '2025-09-01',
            'date_fin_application' => null,
            'id_grade' => 1,
            'id_annee' => 1,
        ]);
    }
}