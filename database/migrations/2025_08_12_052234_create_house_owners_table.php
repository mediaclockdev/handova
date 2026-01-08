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
        Schema::create('house_owners', function (Blueprint $table) {
            $table->id();
            $table->string('house_owner_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email_address');
            $table->string('phone_number')->nullable();
            $table->text('address_of_property')->nullable();
            $table->string('house_plan_name')->nullable();
            $table->date('build_completion_date')->nullable();
            $table->string('assigned_builder_site_manager')->nullable();
            $table->integer('number_of_bedrooms')->nullable();
            $table->integer('number_of_bathrooms')->nullable();
            $table->string('parking')->nullable();
            $table->json('handover_documents')->nullable();// multiple images
            $table->json('floor_plan_upload')->nullable();// multiple images
            $table->string('property_status')->nullable();
            $table->json('tags')->nullable();
            $table->longText('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('house_owners');
    }
};
