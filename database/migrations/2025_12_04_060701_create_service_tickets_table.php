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
        Schema::create('service_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('house_owner_id'); // Store house owner id
            $table->string('title');
            $table->text('description');
            $table->timestamps();

            // Add foreign key if house owners table exists
            $table->foreign('house_owner_id')->references('id')->on('house_owners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_tickets');
    }
};
