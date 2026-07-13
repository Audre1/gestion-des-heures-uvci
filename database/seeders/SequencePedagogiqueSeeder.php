<?php

namespace Database\Seeders;

use App\Models\SequencePedagogique;
use Illuminate\Database\Seeder;

class SequencePedagogiqueSeeder extends Seeder
{
    public function run(): void
    {
        $sequences = [
            // INF101 - Algorithmique et Programmation
            ['titre' => 'Introduction à l\'algorithmique', 'numero_ordre' => 1, 'id_cours' => 1],
            ['titre' => 'Structures de contrôle', 'numero_ordre' => 2, 'id_cours' => 1],
            ['titre' => 'Tableaux et chaînes', 'numero_ordre' => 3, 'id_cours' => 1],
            ['titre' => 'Fonctions et procédures', 'numero_ordre' => 4, 'id_cours' => 1],
            // INF102 - Architecture des Ordinateurs
            ['titre' => 'Histoire et évolution', 'numero_ordre' => 1, 'id_cours' => 2],
            ['titre' => 'Processeur et mémoire', 'numero_ordre' => 2, 'id_cours' => 2],
            ['titre' => 'Système de bus', 'numero_ordre' => 3, 'id_cours' => 2],
            // INF201 - Bases de Données
            ['titre' => 'Introduction aux BD', 'numero_ordre' => 1, 'id_cours' => 4],
            ['titre' => 'Modèle relationnel', 'numero_ordre' => 2, 'id_cours' => 4],
            ['titre' => 'SQL avancé', 'numero_ordre' => 3, 'id_cours' => 4],
            // INF301 - Intelligence Artificielle
            ['titre' => 'Introduction à l\'IA', 'numero_ordre' => 1, 'id_cours' => 7],
            ['titre' => 'Apprentissage automatique', 'numero_ordre' => 2, 'id_cours' => 7],
            ['titre' => 'Réseaux de neurones', 'numero_ordre' => 3, 'id_cours' => 7],
            // MATH101 - Analyse Mathématique
            ['titre' => 'Limites et continuité', 'numero_ordre' => 1, 'id_cours' => 9],
            ['titre' => 'Dérivation et intégration', 'numero_ordre' => 2, 'id_cours' => 9],
            ['titre' => 'Séries numériques', 'numero_ordre' => 3, 'id_cours' => 9],
            // PHYS101 - Physique Générale
            ['titre' => 'Mécanique classique', 'numero_ordre' => 1, 'id_cours' => 11],
            ['titre' => 'Électromagnétisme', 'numero_ordre' => 2, 'id_cours' => 11],
        ];

        foreach ($sequences as $seq) {
            SequencePedagogique::create($seq);
        }
    }
}