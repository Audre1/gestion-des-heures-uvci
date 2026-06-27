<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crée la table 'users' (correspondant à l'entité UTILISATEUR du MCD).
     * Doit être exécutée après la migration des rôles.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('login', 100)->unique();
            $table->string('mot_de_passe');
            $table->timestamp('date_creation')->useCurrent();
            $table->enum('statut_compte', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->rememberToken();
            // FK vers roles.id (string)
            $table->string('id_role');
            $table->foreign('id_role')->references('id')->on('roles')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
