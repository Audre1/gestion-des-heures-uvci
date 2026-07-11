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
        Schema::table('enseignants', function (Blueprint $table) {
            // Modifier le statut de enum (actif, inactif, retraite) vers (Permanent, Vacataire)
            $table->enum('statut', ['Permanent', 'Vacataire'])->default('Permanent')->change();

            // Ajouter le champ taux_horaire_perso
            $table->decimal('taux_horaire_perso', 10, 2)->nullable()->after('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enseignants', function (Blueprint $table) {
            // Revenir à l'ancien statut
            $table->enum('statut', ['actif', 'inactif', 'retraite'])->default('actif')->change();

            // Supprimer le champ taux_horaire_perso
            $table->dropColumn('taux_horaire_perso');
        });
    }
};
