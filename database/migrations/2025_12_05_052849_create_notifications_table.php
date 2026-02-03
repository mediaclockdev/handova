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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('properties_id')->nullable();
            $table->unsignedBigInteger('house_owner_id')->nullable();

            $table->string('title');
            $table->text('body');
            $table->boolean('is_read')->default(0);

            $table->timestamps();

            // Foreign keys
            $table->foreign('properties_id')
                ->references('id')
                ->on('properties')
                ->onDelete('cascade');

            $table->foreign('house_owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['properties_id']);
            $table->dropForeign(['house_owner_id']);
        });

        Schema::dropIfExists('notifications');
    }
};
