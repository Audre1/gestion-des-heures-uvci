<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use Illuminate\Database\Seeder;

class AnneeAcademiqueSeeder extends Seeder
{
    public function run(): void
    {
        AnneeAcademique::create([
            'libelle' => '2025-2026',
            'date_debut' => '2025-09-01',
            'date_fin' => '2026-07-31',
            'statut' => 'en_cours',
        ]);
    }
}