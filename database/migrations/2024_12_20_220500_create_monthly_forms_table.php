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
        Schema::create('monthly_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->unique('donor_id'); // Ensure each donor can only have one monthly form
            $table->foreignId('employee_id')->nullable()
            ->constrained('employees')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()
            ->constrained('departments')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('donation_type', ['financial','inKind','both'])->default('financial');
            $table->text('notes')->nullable();
            $table->enum('collecting_donation_way',['online','location','representative']);
            $table->enum('status',['ongoing','cancelled'])->default('ongoing');
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('cancellation_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_forms');
    }
};
