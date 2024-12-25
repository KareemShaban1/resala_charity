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
        Schema::create('monthly_donations', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()
            ->constrained('employees')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()
            ->constrained('departments')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->enum('collecting_donation_way',['online','location','representative']);
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_donations');
    }
};
