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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g., 'created', 'updated', 'deleted'
            $table->string('model'); // The model name
            $table->unsignedBigInteger('model_id'); // ID of the affected model
            $table->json('changes')->nullable(); // Log changes if any
            $table->unsignedBigInteger('user_id')->nullable(); // User performing the action
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
