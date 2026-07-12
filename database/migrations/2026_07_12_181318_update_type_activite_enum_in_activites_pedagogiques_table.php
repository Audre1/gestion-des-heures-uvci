<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE activites_pedagogiques MODIFY COLUMN type_activite ENUM('creation', 'maj') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE activites_pedagogiques MODIFY COLUMN type_activite ENUM('creation', 'mise_a_jour') NOT NULL");
    }
};
