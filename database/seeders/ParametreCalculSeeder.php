<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\ParametreCalcul;
use Illuminate\Database\Seeder;

class ParametreCalculSeeder extends Seeder
{
    public function run(): void
    {
        $annee = AnneeAcademique::where('statut', 'en_cours')->first();

        if (!$annee) {
            $this->command->warn('Aucune année académique active trouvée. Seeder ignoré.');
            return;
        }

        ParametreCalcul::updateOrCreate(
            ['annee_id' => $annee->id],
            [
                // Règles générales
                'heures_par_credit'     => 10,
                'sequences_par_credit'  => 40,
                'service_statutaire'    => 192,
                'reduction_mise_a_jour' => 50,
            ]
        );

        $this->command->info('Paramètres de calcul initialisés avec succès.');
    }
}
