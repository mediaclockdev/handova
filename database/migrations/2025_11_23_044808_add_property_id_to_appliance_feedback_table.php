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
        Schema::table('appliance_feedback', function (Blueprint $table) {
            if (!Schema::hasColumn('appliance_feedback', 'property_id')) {
                $table->unsignedBigInteger('property_id')->after('id');
                $table->foreign('property_id')
                    ->references('id')->on('properties')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appliance_feedback', function (Blueprint $table) {
            if (Schema::hasColumn('appliance_feedback', 'property_id')) {
                $table->dropForeign(['property_id']);
                $table->dropColumn('property_id');
            }
        });
    }
};
