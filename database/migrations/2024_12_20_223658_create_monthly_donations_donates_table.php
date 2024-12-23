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
            $table->foreignId('monthly_donation_id')->references('id')->on('monthly_donations')
            ->onDelete('cascade');
            $table->foreignId('donation_category_id')->references('id')->on('donation_categories')
            ->onDelete('cascade');
            $table->enum('donation_type', ['Financial','inKind'])->default('Financial');
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
