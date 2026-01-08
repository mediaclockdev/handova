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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name');
            $table->string('plan_type')->nullable(); // e.g. monthly/yearly
            $table->decimal('plan_price', 10, 2)->default(0);
            $table->integer('plan_duration'); // e.g. 3
            $table->string('plan_duration_unit'); // days, months, years
            $table->integer('plan_allowed_listing')->nullable();
            $table->integer('plan_video_upload_limit')->nullable();
            $table->json('plan_additional_feature')->nullable(); // checkbox values
            $table->text('plan_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
