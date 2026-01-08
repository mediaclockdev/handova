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
        Schema::create('house_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name');
            $table->string('storey')->nullable();
            $table->decimal('pricing', 10, 2)->nullable();
            $table->string('house_area')->nullable();
            $table->string('display_location')->nullable();
            $table->string('suburbs')->nullable();
            $table->integer('no_of_bedrooms')->nullable();
            $table->integer('no_of_bathrooms')->nullable();
            $table->string('parking')->nullable();
            $table->string('swimming_pool')->nullable();
            $table->string('floor_plan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('house_plans');
    }
};
