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
        Schema::table('properties', function (Blueprint $table) {
            $table->string('property_title')->after('id');
            $table->string('property_type')->nullable();
            $table->text('address')->nullable();
            $table->string('house_plan_name')->nullable();
            $table->date('build_completion_date')->nullable();
            $table->string('assigned_builder_site_manager')->nullable();
            $table->integer('number_of_bedrooms')->nullable();
            $table->integer('number_of_bathrooms')->nullable();
            $table->string('parking')->nullable(); // e.g., 'Yes/No' or '2 Car'
            $table->boolean('swimming_pool')->default(0);
            $table->json('floor_plan_upload')->nullable(); // store multiple images
            $table->string('property_status')->nullable();
            $table->json('appliances')->nullable(); // store multiple items
            $table->json('tags')->nullable(); // store multiple tags
            $table->longText('internal_notes')->nullable();
            $table->string('compliance_certificate')->nullable(); // store file path
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'property_title',
                'property_type',
                'address',
                'house_plan_name',
                'build_completion_date',
                'assigned_builder_site_manager',
                'number_of_bedrooms',
                'number_of_bathrooms',
                'parking',
                'swimming_pool',
                'floor_plan_upload',
                'property_status',
                'appliances',
                'tags',
                'internal_notes',
                'compliance_certificate'
            ]);
        });
    }
};
