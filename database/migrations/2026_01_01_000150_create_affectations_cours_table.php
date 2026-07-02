<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affectations_cours', function (Blueprint $table) {
            $table->id();
            $table->date('date_affectation');
            $table->unsignedBigInteger('id_enseignant');
            $table->foreign('id_enseignant')->references('id')->on('enseignants')->restrictOnDelete();
            $table->unsignedBigInteger('id_cours');
            $table->foreign('id_cours')->references('id')->on('cours')->restrictOnDelete();
            $table->unsignedBigInteger('id_annee');
            $table->foreign('id_annee')->references('id')->on('annees_academiques')->restrictOnDelete();
            $table->unique(['id_enseignant', 'id_cours', 'id_annee']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affectations_cours');
    }
};
