<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('affectations_cours', function (Blueprint $table) {
            // Nouvelle contrainte unique : un (cours + niveau + semestre + année) pour un seul enseignant
            // Note : l'ancienne contrainte (id_enseignant, id_cours, id_annee) a déjà été supprimée
            $table->unique(['id_cours', 'niveau', 'semestre', 'id_annee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affectations_cours', function (Blueprint $table) {
            $table->dropUnique(['id_cours', 'niveau', 'semestre', 'id_annee']);
        });
    }
};