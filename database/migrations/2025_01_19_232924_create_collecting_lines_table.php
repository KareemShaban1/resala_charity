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
        Schema::create('collecting_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representative_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('employees')
            ->nullOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('area_group_id')->constrained('area_groups')->onDelete('cascade');
            $table->date('collecting_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collecting_lines');
    }
};
