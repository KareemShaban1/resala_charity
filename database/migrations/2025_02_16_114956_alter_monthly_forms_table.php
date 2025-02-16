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
        //
        Schema::table('monthly_forms', function (Blueprint $table) {
            $table->date('form_date')
            ->default(now())->after('donation_type');
            $table->foreignId('follow_up_department_id')
            ->nullable()
            ->after('form_date') // Change position to follow `form_date`
            ->constrained('departments')
            ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
