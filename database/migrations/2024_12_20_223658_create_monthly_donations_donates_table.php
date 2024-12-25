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
        Schema::create('monthly_donations_donates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_donation_id')
            ->constrained('monthly_donations')
            ->onDelete('cascade');

            $table->enum('donation_type', ['Financial','inKind'])->default('Financial');

            $table->foreignId('donation_category_id')
            ->nullable() // Allow null values
            ->constrained('donation_categories')
            ->onDelete('cascade');
            $table->string('item_name')->nullable();
            $table->string('amount');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_donations_donates');
    }
};
