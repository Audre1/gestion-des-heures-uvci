<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annees_academiques', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('libelle', 20)->unique(); // ex: 2025-2026
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['en_cours', 'cloturee', 'a_venir'])->default('a_venir');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annees_academiques');
    }
};
