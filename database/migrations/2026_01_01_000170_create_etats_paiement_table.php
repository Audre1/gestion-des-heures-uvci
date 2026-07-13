<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etats_paiement', function (Blueprint $table) {
            $table->id();
            $table->string('numero_paiement')->unique();
            $table->timestamp('date_generation')->useCurrent();
            $table->string('periode', 50);
            $table->decimal('montant_total', 12, 2);
            $table->enum('statut', ['en_attente', 'valide', 'paye', 'rejete'])->default('en_attente');
            $table->string('format_export', 20)->nullable();
            $table->unsignedBigInteger('id_enseignant');
            $table->foreign('id_enseignant')->references('id')->on('enseignants')->restrictOnDelete();
            $table->unsignedBigInteger('id_annee');
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
