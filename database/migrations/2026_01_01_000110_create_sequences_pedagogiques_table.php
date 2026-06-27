<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequences_pedagogiques', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('titre', 255);
            $table->integer('numero_ordre');
            $table->string('id_cours');
            $table->foreign('id_cours')->references('id')->on('cours')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequences_pedagogiques');
    }
};
