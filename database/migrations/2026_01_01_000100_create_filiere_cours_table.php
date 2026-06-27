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
            $table->string('id_filiere');
            $table->foreign('id_filiere')->references('id')->on('filieres')->cascadeOnDelete();
            $table->string('id_cours');
            $table->foreign('id_cours')->references('id')->on('cours')->cascadeOnDelete();
            $table->primary(['id_filiere', 'id_cours']);
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
