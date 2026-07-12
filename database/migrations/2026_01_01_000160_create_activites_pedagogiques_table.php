<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activites_pedagogiques', function (Blueprint $table) {
            $table->id();

            // Type : uniquement création ou mise à jour
            $table->enum('type_activite', ['creation', 'maj']);

            $table->date('date_activite');

            // Statut aligné sur le métier
            $table->enum('statut', ['en_cours', 'validee', 'rejetee'])->default('en_cours');

            // Calculés automatiquement depuis les paramètres
            $table->decimal('coefficient', 5, 3);
            $table->integer('nb_sequences');
            $table->decimal('volume_horaire', 8, 2);

            // Relations
            $table->unsignedBigInteger('id_affectation');
            $table->foreign('id_affectation')
                ->references('id')
                ->on('affectations_cours')
                ->restrictOnDelete();

            $table->unsignedBigInteger('id_ressource')->nullable();
            $table->foreign('id_ressource')
                ->references('id')
                ->on('ressources_pedagogiques')
                ->nullOnDelete();

            $table->unsignedBigInteger('id_niveau')->nullable();
            $table->foreign('id_niveau')
                ->references('id')
                ->on('niveaux_complexite')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites_pedagogiques');
    }
};
