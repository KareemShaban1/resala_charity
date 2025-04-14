<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collecting_lines', function (Blueprint $table) {
            // Make columns nullable
            $table->foreignId('representative_id')->nullable()->change();
            $table->foreignId('employee_id')->nullable()->change();

            // Add status column
            $table->enum('status', ['open', 'closed', 'pending'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('collecting_lines', function (Blueprint $table) {
            // Revert columns back to not nullable
            $table->foreignId('representative_id')->nullable(false)->change();
            $table->foreignId('employee_id')->nullable(false)->change();

            // Drop status column
            $table->dropColumn('status');
        });
    }
};

