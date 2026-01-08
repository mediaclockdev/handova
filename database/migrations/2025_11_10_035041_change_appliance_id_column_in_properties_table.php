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
            Schema::table('properties', function (Blueprint $table) {
                // 1️⃣ Drop the foreign key constraint (replace constraint name if needed)
                $table->dropForeign(['appliance_id']);

                // 2️⃣ Drop the existing column
                $table->dropColumn('appliance_id');
            });

            Schema::table('properties', function (Blueprint $table) {
                // 3️⃣ Add a new JSON column
                $table->json('appliance_id')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Rollback logic — back to a single foreign key column
            $table->unsignedBigInteger('appliance_id')->nullable();
            $table->foreign('appliance_id')->references('id')->on('appliances')->onDelete('cascade');
        });
    }
};
