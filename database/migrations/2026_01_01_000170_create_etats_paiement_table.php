<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etats_paiement', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->timestamp('date_generation')->useCurrent();
            $table->string('periode', 50); // ex: Octobre 2025, S1 2025-2026
            $table->decimal('montant_total', 12, 2);
            $table->enum('statut', ['brouillon', 'valide', 'paye', 'rejete'])->default('brouillon');
            $table->string('format_export', 20)->nullable(); // ex: PDF, EXCEL, CSV
            $table->string('id_enseignant');
            $table->foreign('id_enseignant')->references('id')->on('enseignants')->restrictOnDelete();
            $table->string('id_annee');
            $table->foreign('id_annee')->references('id')->on('annees_academiques')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etats_paiement');
    }
};
