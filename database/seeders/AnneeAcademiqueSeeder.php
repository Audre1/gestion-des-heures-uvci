<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use Illuminate\Database\Seeder;

class AnneeAcademiqueSeeder extends Seeder
{
    public function run(): void
    {
        $annees = [
            ['libelle' => '2024-2025', 'date_debut' => '2024-09-01', 'date_fin' => '2025-07-31', 'statut' => 'cloturee'],
            ['libelle' => '2025-2026', 'date_debut' => '2025-09-01', 'date_fin' => '2026-07-31', 'statut' => 'en_cours'],
            ['libelle' => '2026-2027', 'date_debut' => '2026-09-01', 'date_fin' => '2027-07-31', 'statut' => 'a_venir'],
        ];

        foreach ($annees as $annee) {
            AnneeAcademique::create($annee);
        }
    }
}