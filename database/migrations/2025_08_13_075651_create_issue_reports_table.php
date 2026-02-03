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
        Schema::create('issue_reports', function (Blueprint $table) {
            $table->id();
            $table->string('issue_number')->unique();
            $table->unsignedBigInteger('properties_id');
            $table->text('issue_details');
            $table->unsignedBigInteger('reported_by');
            $table->date('reported_date')->default(now());
            $table->enum('assigned_to_service_provider', ['yes', 'no'])->default('no');
            $table->string('service_provider')->nullable();
            $table->foreign('properties_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_reports');
    }
};
