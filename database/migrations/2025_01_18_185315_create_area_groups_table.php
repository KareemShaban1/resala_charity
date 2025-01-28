<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create the area_groups table
        Schema::create('area_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create the pivot table area_group_members
        Schema::create('area_group_members', function (Blueprint $table) {
            $table->foreignId('area_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->primary(['area_group_id', 'area_id']); // Composite primary key
        });
    }

    public function down()
    {
        Schema::dropIfExists('area_group_members');
        Schema::dropIfExists('area_groups');
    }
};