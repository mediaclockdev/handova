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
        Schema::table('house_owners', function (Blueprint $table) {
            $table->unsignedBigInteger('properties_id')->nullable()->after('house_owner_id');
            $table->foreign('properties_id')->references('id')->on('properties')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('house_owners', function (Blueprint $table) {
            $table->dropForeign(['properties_id']);
            $table->dropColumn('properties_id');
        });
    }
};
