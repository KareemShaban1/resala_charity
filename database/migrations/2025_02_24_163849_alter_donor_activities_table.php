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
        Schema::table('donor_activities', function (Blueprint $table) {
            //
            $table->dropColumn('status');
            $table->foreignId('activity_status_id')
            ->after('date_time')
            ->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donor_activities', function (Blueprint $table) {
            //

            $table->dropForeign(['activity_status_id']);
            $table->dropColumn('activity_status_id');
            $table->string('status')->nullable()->after('response');
        });
    }
};
