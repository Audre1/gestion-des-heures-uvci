<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activites_pedagogiques', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('type_activite', 100); // ex: CM, TD, TP, Projet...
            $table->date('date_activite');
            $table->enum('statut', ['planifie', 'realise', 'annule'])->default('planifie');
            $table->decimal('coefficient', 5, 2)->default(1);
            $table->integer('nb_sequences')->default(1);
            $table->decimal('volume_horaire', 5, 2); // en heures
            $table->string('id_affectation');
            $table->foreign('id_affectation')->references('id')->on('affectations_cours')->restrictOnDelete();
            $table->string('id_ressource')->nullable();
            $table->foreign('id_ressource')->references('id')->on('ressources_pedagogiques')->nullOnDelete();
            $table->string('id_niveau')->nullable();
            $table->foreign('id_niveau')->references('id')->on('niveaux_complexite')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites_pedagogiques');
    }
};
