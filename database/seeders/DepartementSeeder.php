<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        Departement::create([
            'nom_departement' => 'Département Informatique',
        ]);
    }
}