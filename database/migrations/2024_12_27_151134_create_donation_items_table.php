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
        Schema::create('donation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained()->onDelete('cascade');
            $table->enum('donation_type', ['financial','inKind'])->default('financial');
            $table->enum('donation_item_type', ['normal','monthly','gathered'])->default('normal');
            $table->foreignId('donation_category_id')->nullable()->constrained('donation_categories')->onDelete('cascade');
            $table->string('item_name')->nullable();
            $table->string('amount');
            $table->string('financial_receipt_number')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_items');
    }
};
