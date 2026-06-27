<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cours', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('code_cours', 20)->unique();
            $table->string('intitule', 255);
            $table->integer('nombre_heures');
            $table->integer('nombre_credits');
            $table->enum('semestre', ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8']);
            $table->string('niveau', 20); // ex: L1, L2, L3, M1, M2
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
