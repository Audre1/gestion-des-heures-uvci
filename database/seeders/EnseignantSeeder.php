<?php

namespace Database\Seeders;

use App\Models\Enseignant;
use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EnseignantSeeder extends Seeder
{
    public function run(): void
    {
        $enseignantRole = Role::where('code', 'enseignant')->first();
        if (!$enseignantRole) {
            $this->command->error('Le rôle enseignant n\'existe pas.');
            return;
        }

        $enseignants = [
            ['nom' => 'Kouassi', 'prenom' => 'Jean', 'email' => 'jean.kouassi@uvci.ci', 'telephone' => '+225 01 01 01 01', 'matricule' => 'ENS001', 'statut' => 'Permanent', 'date_recrutement' => '2019-09-01', 'id_grade' => 1, 'id_departement' => 1],
            ['nom' => 'Koné', 'prenom' => 'Moussa', 'email' => 'moussa.kone@uvci.ci', 'telephone' => '+225 01 01 01 02', 'matricule' => 'ENS002', 'statut' => 'Permanent', 'date_recrutement' => '2020-10-15', 'id_grade' => 2, 'id_departement' => 1],
            ['nom' => 'Touré', 'prenom' => 'Fatou', 'email' => 'fatou.toure@uvci.ci', 'telephone' => '+225 01 01 01 03', 'matricule' => 'ENS003', 'statut' => 'Permanent', 'date_recrutement' => '2021-01-10', 'id_grade' => 2, 'id_departement' => 1],
            ['nom' => 'Diallo', 'prenom' => 'Amadou', 'email' => 'amadou.diallo@uvci.ci', 'telephone' => '+225 01 01 01 04', 'matricule' => 'ENS004', 'statut' => 'Permanent', 'date_recrutement' => '2018-11-20', 'id_grade' => 1, 'id_departement' => 2],
            ['nom' => 'N\'Guessan', 'prenom' => 'Marie', 'email' => 'marie.nguessan@uvci.ci', 'telephone' => '+225 01 01 01 05', 'matricule' => 'ENS005', 'statut' => 'Vacataire', 'date_recrutement' => '2022-03-05', 'id_grade' => 3, 'id_departement' => 3],
            ['nom' => 'Bamba', 'prenom' => 'Souleymane', 'email' => 'souleymane.bamba@uvci.ci', 'telephone' => '+225 01 01 01 06', 'matricule' => 'ENS006', 'statut' => 'Permanent', 'date_recrutement' => '2020-06-01', 'id_grade' => 2, 'id_departement' => 4],
            ['nom' => 'Yao', 'prenom' => 'Christine', 'email' => 'christine.yao@uvci.ci', 'telephone' => '+225 01 01 01 07', 'matricule' => 'ENS007', 'statut' => 'Vacataire', 'date_recrutement' => '2021-09-12', 'id_grade' => 3, 'id_departement' => 5],
        ];

        foreach ($enseignants as $data) {
            // Générer le login
            $premierPrenom = explode(' ', trim($data['prenom']))[0];
            $login = strtolower($premierPrenom . '.' . $data['nom']);
            $login = Str::slug($login, '.');

            // Vérifier l'unicité du login
            $counter = 1;
            $originalLogin = $login;
            while (Utilisateur::where('login', $login)->exists()) {
                $login = $originalLogin . $counter;
                $counter++;
            }

            // Créer l'utilisateur
            $user = Utilisateur::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'login' => $login,
                'mot_de_passe' => Hash::make('password123'),
                'id_role' => $enseignantRole->id,
                'statut_compte' => 'actif',
            ]);

            // Créer l'enseignant
            Enseignant::create([
                'matricule' => $data['matricule'],
                'statut' => $data['statut'],
                'date_recrutement' => $data['date_recrutement'],
                'id_grade' => $data['id_grade'],
                'id_departement' => $data['id_departement'],
                'id_utilisateur' => $user->id,
            ]);
        }

        $this->command->info('Enseignants créés avec succès.');
    }
}