<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['code' => 'admin', 'libelle' => 'Administrateur'],
            ['code' => 'secretaire', 'libelle' => 'Secrétaire'],
            ['code' => 'enseignant', 'libelle' => 'Enseignant']
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['code' => $role['code']],
                ['libelle' => $role['libelle']]
            );
        }
    }
}
