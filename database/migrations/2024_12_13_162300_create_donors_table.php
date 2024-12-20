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
            $table->string('name')->unique();
            $table->foreignId('governorate_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->foreignId('area_id')->constrained();
                       $table->string('street')->nullable();
            $table->text('address');           
            $table->boolean('active')->default(true);
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
