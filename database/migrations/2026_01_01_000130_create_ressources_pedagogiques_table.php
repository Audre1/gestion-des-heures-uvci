<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ressources_pedagogiques', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 255);
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamp('date_modification')->nullable()->useCurrentOnUpdate();
            $table->unsignedBigInteger('id_sequence');
            $table->foreign('id_sequence')->references('id')->on('sequences_pedagogiques')->cascadeOnDelete();
            $table->unsignedBigInteger('id_type');
            $table->foreign('id_type')->references('id')->on('type_ressources')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ressources_pedagogiques');
    }
};
