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
        Schema::table('issue_reports', function (Blueprint $table) {
            $table->string('issue_title')->nullable();
            $table->string('issue_category')->nullable();
            $table->string('issue_location')->nullable();
            $table->string('customer_contact')->nullable();
            $table->string('image')->nullable(); // for uploaded image path
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issue_reports', function (Blueprint $table) {
            $table->dropColumn([
                'issue_title',
                'issue_category',
                'issue_location',
                'customer_contact',
                'image'
            ]);
        });
    }
};
