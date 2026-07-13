<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            GradeSeeder::class,
            TypeRessourceSeeder::class,
            NiveauComplexiteSeeder::class,
            AdminSeeder::class,

            // Données de démonstration
            DepartementSeeder::class,
            AnneeAcademiqueSeeder::class,
            CoursSeeder::class,
            EnseignantSeeder::class,
            FiliereSeeder::class,

            // Dépend des années et grades
            TauxHoraireSeeder::class,
            ParametreCalculSeeder::class,

            // Dépend des cours
            SequencePedagogiqueSeeder::class,
            RessourcePedagogiqueSeeder::class,
        ]);
    }
}