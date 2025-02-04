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
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('donation_category', ['normal','monthly','gathered','normal_and_monthly'])->default('normal');
            $table->enum('donation_type', ['financial','inKind','both'])->default('financial');
            $table->string('date');
            $table->string('alternate_date')->nullable();
            $table->enum('status', ['collected','not_collected','followed_up','cancelled'])->default('not_collected');
            $table->text('collecting_time')->nullable();
            $table->text('notes')->nullable();
            $table->string('reporting_way')->nullable();
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
