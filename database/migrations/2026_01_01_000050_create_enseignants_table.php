<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enseignants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('matricule', 50)->unique();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 150)->unique();
            $table->string('telephone', 20)->nullable();
            $table->enum('statut', ['actif', 'inactif', 'retraite'])->default('actif');
            $table->date('date_recrutement');
            $table->string('id_grade');
            $table->foreign('id_grade')->references('id')->on('grades')->restrictOnDelete();
            $table->string('id_departement');
            $table->foreign('id_departement')->references('id')->on('departements')->restrictOnDelete();
            $table->string('id_utilisateur');
            $table->foreign('id_utilisateur')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enseignants');
    }
};
