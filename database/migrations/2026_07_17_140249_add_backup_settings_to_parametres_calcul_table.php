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
        Schema::table('parametres_calcul', function (Blueprint $table) {
            $table->integer('sauvegarde_auto_delai')->default(24)->after('reduction_mise_a_jour');
            $table->integer('sauvegarde_auto_rotation')->default(7)->after('sauvegarde_auto_delai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parametres_calcul', function (Blueprint $table) {
            $table->dropColumn(['sauvegarde_auto_delai', 'sauvegarde_auto_rotation']);
        });
    }
};
