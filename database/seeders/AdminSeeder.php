<?php

namespace Database\Seeders;

use App\Models\Enseignant;
use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le rôle admin
        $role = Role::all();

        if (!$role) {
            $this->command->error('Le rôle admin n\'existe pas. Exécutez d\'abord RoleSeeder.');
            return;
        }

        // Créer ou mettre à jour l'utilisateur admin
        Utilisateur::updateOrCreate(
            ['email' => 'admin@uvci.ci'],
            [
                'nom' => 'Administrateur',
                'prenom' => 'Système',
                'login' => 'admin',
                'mot_de_passe' => Hash::make('admin123'),
                'telephone' => '+225 01 02 03 04 05',
                'statut_compte' => 'actif',
                'id_role' => 1,
            ]
        );

        Utilisateur::updateOrCreate(
            ['email' => 'secretaire@uvci.ci'],
            [
                'nom' => 'Secretaire',
                'prenom' => 'UVCI',
                'login' => 'secretaire',
                'mot_de_passe' => Hash::make('secretaire123'),
                'telephone' => '+225 07 07 07 07 07',
                'statut_compte' => 'actif',
                'id_role' => 2,
            ]
        );


        $user = Utilisateur::updateOrCreate(
            ['email' => 'enseignant@uvci.ci'],
            [
                'nom' => 'Enseignant',
                'prenom' => 'UVCI',
                'login' => 'enseignant',
                'mot_de_passe' => Hash::make('enseignant123'),
                'telephone' => '+225 07 07 07 07 07',
                'statut_compte' => 'actif',
                'id_role' => 3,
            ]
        );

        // Créer l'enseignant
        Enseignant::create([
            'matricule' => 'ENS-001',
            'statut' => 'Permanent',
            'date_recrutement' => now(),
            'id_grade' => 1,
            'id_departement' => 1,
            'id_utilisateur' => $user->id,
        ]);

        // Afficher les informations de connexion admin
        $this->command->info('Utilisateur admin créé avec succès.');
        $this->command->info('Email: admin@uvci.ci || Login: admin');
        $this->command->info('Mot de passe: admin123');

        // Afficher les informations de connexion secretaire
        $this->command->info('Utilisateur secretaire créé avec succès.');
        $this->command->info('Email: secretaire@uvci.ci || Login: secretaire');
        $this->command->info('Mot de passe: secretaire123');

        // Afficher les informations de connexion enseignant
        $this->command->info('Utilisateur enseignant créé avec succès.');
        $this->command->info('Email: enseignant@uvci.ci || Login: enseignant');
        $this->command->info('Mot de passe: enseignant123');
    }
}
