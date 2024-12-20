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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->text('notes')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('receipt')->nullable();
            $table->string('date')->nullable()->comment('Format: MM-DD');
            $table->boolean('active')->default(true);
            $table->string('donate_date')->nullable()->comment('Format: MM-DD for monthly donors');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
