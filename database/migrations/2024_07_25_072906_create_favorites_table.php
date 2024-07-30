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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uid');
            $table->uuid('store_uid');

            $table->timestamps();

            $table->index('user_uid');
            $table->foreign('user_uid')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreign('store_uid')->references('uuid')->on('stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
