<?php

namespace Database\Seeders;

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
        $adminRole = Role::where('code', 'admin')->first();

        if (!$adminRole) {
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
                'id_role' => $adminRole->id,
            ]
        );

        $this->command->info('Utilisateur admin créé avec succès.');
        $this->command->info('Email: admin@uvci.ci');
        $this->command->info('Mot de passe: admin123');
    }
}
