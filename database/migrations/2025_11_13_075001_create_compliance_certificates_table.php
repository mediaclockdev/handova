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
        Schema::create('compliance_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('certification_title');
            $table->string('compliance_type')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->date('date_of_issue')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('property_area')->nullable();
            $table->json('attachments')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_certificates');
    }
};
