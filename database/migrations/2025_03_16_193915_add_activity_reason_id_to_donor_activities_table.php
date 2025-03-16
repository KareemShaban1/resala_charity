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
        Schema::table('donor_activities', function (Blueprint $table) {
            // Check if the column exists before modifying it
            if (Schema::hasColumn('donor_activities', 'activity_reason_id')) {
                DB::statement("ALTER TABLE donor_activities MODIFY COLUMN activity_reason_id BIGINT UNSIGNED NULL AFTER activity_status_id");
            } else {
                Schema::table('donor_activities', function (Blueprint $table) {
                    $table->foreignId('activity_reason_id')
                        ->nullable()
                        ->constrained('activity_reasons')
                        ->nullOnDelete()
                        ->after('activity_status_id');
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donor_activities', function (Blueprint $table) {
            //

        });
    }
};
