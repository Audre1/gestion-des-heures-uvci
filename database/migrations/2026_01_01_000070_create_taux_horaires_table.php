<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taux_horaires', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->decimal('montant', 10, 2);
            $table->string('devise', 10)->default('XOF');
            $table->date('date_application');
            $table->date('date_fin_application')->nullable();
            $table->string('id_grade');
            $table->foreign('id_grade')->references('id')->on('grades')->restrictOnDelete();
            $table->string('id_annee');
            $table->foreign('id_annee')->references('id')->on('annees_academiques')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taux_horaires');
    }
};
