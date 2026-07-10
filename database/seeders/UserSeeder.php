<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email' => 'enseignant@test.com',
            'login' => 'enseignant01',
            'mot_de_passe' => Hash::make('password123'),
            'statut_compte' => 'actif',
            'id_role' => 3,
        ]);
    }
}