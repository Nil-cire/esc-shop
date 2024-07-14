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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('google_id')->nullable();
            $table->string('apple_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('name');
            $table->integer('buy_count')->default(0);
            $table->integer('reported_count')->default(0);
            $table->boolean('register_completed')->default(false);
            $table->string('e-mail')->unique();
            $table->timestamps();
            // $table->timestamp('created_at');
            // $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
