<?php

namespace Database\Seeders;

use App\Models\RessourcePedagogique;
use Illuminate\Database\Seeder;

class RessourcePedagogiqueSeeder extends Seeder
{
    public function run(): void
    {
        $ressources = [
            // Séquence 1 - INF101
            ['titre' => 'Cours - Introduction à l\'algorithmique', 'id_sequence' => 1, 'id_type' => 1],
            ['titre' => 'Exercices - Introduction', 'id_sequence' => 1, 'id_type' => 3],
            ['titre' => 'Vidéo explicative - Algorithmes', 'id_sequence' => 1, 'id_type' => 2],
            // Séquence 2 - INF101
            ['titre' => 'Cours - Structures de contrôle', 'id_sequence' => 2, 'id_type' => 1],
            ['titre' => 'Exercices corrigés - Boucles', 'id_sequence' => 2, 'id_type' => 3],
            // Séquence 3 - INF101
            ['titre' => 'Cours - Tableaux et chaînes', 'id_sequence' => 3, 'id_type' => 1],
            // Séquence 4 - INF101
            ['titre' => 'Cours - Fonctions', 'id_sequence' => 4, 'id_type' => 1],
            ['titre' => 'Mini-projet - Programmation', 'id_sequence' => 4, 'id_type' => 6],
            // Séquence 5 - INF102
            ['titre' => 'Cours - Histoire des ordinateurs', 'id_sequence' => 5, 'id_type' => 1],
            ['titre' => 'Schémas - Architecture', 'id_sequence' => 5, 'id_type' => 3],
            // Séquence 8 - INF201
            ['titre' => 'Cours - Introduction aux BD', 'id_sequence' => 8, 'id_type' => 1],
            ['titre' => 'TP - SQL', 'id_sequence' => 8, 'id_type' => 5],
            ['titre' => 'Exercices - Modélisation', 'id_sequence' => 8, 'id_type' => 3],
            // Séquence 11 - INF301
            ['titre' => 'Cours - Introduction à l\'IA', 'id_sequence' => 11, 'id_type' => 1],
            ['titre' => 'TP - Machine Learning', 'id_sequence' => 11, 'id_type' => 5],
            // Séquence 14 - MATH101
            ['titre' => 'Cours - Limites et continuité', 'id_sequence' => 14, 'id_type' => 1],
            ['titre' => 'Exercices - Dérivées', 'id_sequence' => 14, 'id_type' => 3],
        ];

        foreach ($ressources as $r) {
            RessourcePedagogique::create($r);
        }
    }
}