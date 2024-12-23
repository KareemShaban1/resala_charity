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
            $table->string('number');
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('date')->comment('Format: MM-DD');
            $table->text('notes')->nullable();
            $table->enum('collecting_donation_way',['online','location','representative']);
            // $table->foreignId('collected_by')->nullable()->constrained('users')->onDelete('cascade');
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
