<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres_calcul', function (Blueprint $table) {
            $table->id();

            // Clé étrangère année académique
            $table->foreignId('annee_id')
                  ->constrained('annees_academiques')
                  ->onDelete('cascade');

            // Règles générales
            $table->integer('heures_par_credit')->default(10);
            $table->integer('sequences_par_credit')->default(40);
            $table->integer('service_statutaire')->default(192);
            $table->integer('reduction_mise_a_jour')->default(50);

            // Coefficients création
            $table->decimal('coeff_creation_niv1', 5, 3)->default(0.400);
            $table->decimal('coeff_creation_niv2', 5, 3)->default(0.750);
            $table->decimal('coeff_creation_niv3', 5, 3)->default(1.500);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres_calcul');
    }
};