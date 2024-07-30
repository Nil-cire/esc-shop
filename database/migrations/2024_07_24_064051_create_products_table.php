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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable(false);
            $table->string("store_uid");
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("spec")->nullable();
            $table->string("note")->nullable();
            $table->decimal("price")->unsigned();
            $table->decimal("special_price")->unsigned()->nullable();
            $table->timestamp("special_price_start")->nullable();
            $table->timestamp("special_price_end")->nullable();
            $table->unsignedSmallInteger("stock")->default(0);
            $table->string('image_url')->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_enable')->default(true);

            $table->timestamps();

            $table->index('store_uid');
            $table->foreign('store_uid')->references('uuid')->on('stores')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
