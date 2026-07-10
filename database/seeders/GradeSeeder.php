<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['libelle' => 'Professeur'],
            ['libelle' => 'Maître-Assistant'],
            ['libelle' => 'Assistant'],
        ];

        foreach ($grades as $grade) {
            Grade::firstOrCreate(
                ['libelle' => $grade['libelle']],
                $grade
            );
        }

        $this->command->info('Grades initialisés avec succès.');
    }
}
