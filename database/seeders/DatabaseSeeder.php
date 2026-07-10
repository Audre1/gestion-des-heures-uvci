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
    UserSeeder::class,
    DepartementSeeder::class,
    GradeSeeder::class,
    EnseignantSeeder::class,
    AnneeAcademiqueSeeder::class,
    TauxHoraireSeeder::class,
    CoursSeeder::class,
    SequencePedagogiqueSeeder::class,
    TypeRessourceSeeder::class,
    RessourcePedagogiqueSeeder::class,
    AffectationCoursSeeder::class,
    NiveauComplexiteSeeder::class,
    ActivitePedagogiqueSeeder::class,
]);
}
}
