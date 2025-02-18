<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE donors MODIFY COLUMN donor_category ENUM('normal', 'special', 'random') DEFAULT 'normal'");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE donors MODIFY COLUMN donor_category ENUM('normal', 'special') DEFAULT 'normal'");

        });
    }
};
