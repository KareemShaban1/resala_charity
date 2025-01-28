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
        Schema::create('donation_collectings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained()->onDelete('cascade');
            $table->date('collecting_date');
            // $table->string('financial_receipt_number')->nullable();
            $table->string('in_kind_receipt_number')->nullable();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('collecting_way',['online','location','representative'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_collectings');
    }
};
