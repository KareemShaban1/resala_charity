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
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable(); // Ensure unsigned and nullable
            $table->foreign('parent_id')
                ->references('id')
                ->on('donors')
                ->nullOnDelete(); // Set the foreign key constraint with on delete set null
            $table->string('name');
            $table->foreignId('governorate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('street')->nullable();
            $table->text('address')->nullable();
            $table->boolean('active')->default(true);
            $table->enum('donor_type', ['normal', 'monthly'])->default('normal');
            $table->enum('donor_category', ['normal', 'special'])->default('normal');
            $table->string('monthly_donation_day')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
