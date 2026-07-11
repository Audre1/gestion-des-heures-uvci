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
        Schema::create('filiere_cours', function (Blueprint $table) {
            $table->unsignedBigInteger('id_filiere');
            $table->foreign('id_filiere')->references('id')->on('filieres')->cascadeOnDelete();
            $table->unsignedBigInteger('id_cours');
            $table->foreign('id_cours')->references('id')->on('cours')->cascadeOnDelete();
            $table->enum('semestre', ['S1', 'S2', 'S3', 'S4', 'S5', 'S6']);
            $table->enum('niveau', ['L1', 'L2', 'L3', 'M1', 'M2']);
            $table->primary(['id_filiere', 'id_cours', 'semestre', 'niveau']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filiere_cours');
    }
};
