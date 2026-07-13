<?php

namespace Database\Seeders;

use App\Models\Cours;
use Illuminate\Database\Seeder;

class CoursSeeder extends Seeder
{
    public function run(): void
    {
        $cours = [
            ['code_cours' => 'INF101', 'intitule' => 'Algorithmique et Programmation', 'nombre_heures' => 45, 'nombre_credits' => 4],
            ['code_cours' => 'INF102', 'intitule' => 'Architecture des Ordinateurs', 'nombre_heures' => 40, 'nombre_credits' => 3],
            ['code_cours' => 'INF103', 'intitule' => 'Systèmes d\'Exploitation', 'nombre_heures' => 35, 'nombre_credits' => 3],
            ['code_cours' => 'INF201', 'intitule' => 'Bases de Données', 'nombre_heures' => 40, 'nombre_credits' => 4],
            ['code_cours' => 'INF202', 'intitule' => 'Réseaux Informatiques', 'nombre_heures' => 40, 'nombre_credits' => 3],
            ['code_cours' => 'INF203', 'intitule' => 'Génie Logiciel', 'nombre_heures' => 35, 'nombre_credits' => 3],
            ['code_cours' => 'INF301', 'intitule' => 'Intelligence Artificielle', 'nombre_heures' => 45, 'nombre_credits' => 4],
            ['code_cours' => 'INF302', 'intitule' => 'Sécurité Informatique', 'nombre_heures' => 30, 'nombre_credits' => 3],
            ['code_cours' => 'MATH101', 'intitule' => 'Analyse Mathématique', 'nombre_heures' => 50, 'nombre_credits' => 5],
            ['code_cours' => 'MATH102', 'intitule' => 'Algèbre Linéaire', 'nombre_heures' => 45, 'nombre_credits' => 4],
            ['code_cours' => 'PHYS101', 'intitule' => 'Physique Générale', 'nombre_heures' => 40, 'nombre_credits' => 4],
            ['code_cours' => 'GEST101', 'intitule' => 'Introduction à la Gestion', 'nombre_heures' => 30, 'nombre_credits' => 3],
            ['code_cours' => 'LANG101', 'intitule' => 'Anglais Technique', 'nombre_heures' => 25, 'nombre_credits' => 2],
            ['code_cours' => 'LANG102', 'intitule' => 'Communication', 'nombre_heures' => 20, 'nombre_credits' => 2],
        ];

        foreach ($cours as $c) {
            Cours::create($c);
        }
    }
}