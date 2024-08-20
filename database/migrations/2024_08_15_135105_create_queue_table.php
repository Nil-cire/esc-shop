<?php

use App\Enums\QueueMode;
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
        Schema::create('queue', function (Blueprint $table) {
            $table->uuid("uuid")->primary();
            $table->uuid("scan_uuid")->unique();
            // $table->uuid("update_mode_uuid")->nullable();
            // $table->boolean("is_update_mode")->default(false);
            // $table->uuid("one_shot_mode_uuid")->nullable();
            // $table->boolean("is_one_shot_mode")->default(false);
            $table->uuid("store_uuid")->unique()->nullable();
            $table->unsignedSmallInteger("current_number")->default(0);
            $table->unsignedSmallInteger("await_number")->default(0);
            $table->string("store_name");
            $table->string("note")->nullable();
            $table->string("mode")->default('normal');
            // $table->unsignedSmallInteger("terminal_number")->nullable();
            $table->timestamp("start_time");
            $table->timestamp("end_time");
            $table->boolean("is_pause")->default(false);
            $table->string("pause_message")->nullable();
            $table->boolean("is_close")->default(false);
            $table->string("terminal_message")->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue');
    }
};
