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
        Schema::table('niveaux_complexite', function (Blueprint $table) {
            $table->decimal('coefficient', 5, 2)->default(1.00)->after('libelle');
            $table->text('description')->nullable()->after('coefficient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('niveaux_complexite', function (Blueprint $table) {
            $table->dropColumn(['coefficient', 'description']);
        });
    }
};
