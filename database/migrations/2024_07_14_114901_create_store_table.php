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
        Schema::create('stores', function (Blueprint $table) {
            $uuid = Str::uuid();
            $table->id();
            $table->uuid('uuid')->unique()->default($uuid);
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->boolean('is_open')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('banner_image_path')->nullable();
            // $table->integer('traded_count')->default(0);
            // $table->integer('report_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store');
    }
};
