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
            $table->dropColumn(['coeff_creation_niv1', 'coeff_creation_niv2', 'coeff_creation_niv3']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parametres_calcul', function (Blueprint $table) {
            $table->decimal('coeff_creation_niv1', 5, 3)->default(0.400);
            $table->decimal('coeff_creation_niv2', 5, 3)->default(0.750);
            $table->decimal('coeff_creation_niv3', 5, 3)->default(1.500);
        });
    }
};
