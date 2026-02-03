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
        Schema::table('appliances', function (Blueprint $table) {
            DB::statement('ALTER TABLE appliances MODIFY appliances_images TEXT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appliances', function (Blueprint $table) {
            DB::statement('ALTER TABLE appliances MODIFY appliances_images VARCHAR(255)');
        });
    }
};
