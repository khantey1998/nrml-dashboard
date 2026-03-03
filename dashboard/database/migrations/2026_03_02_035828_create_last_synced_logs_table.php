<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('last_synced_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('surveillance_id');
            $table->dateTime('last_synced_date');

            $table->foreign('surveillance_id')
                ->references('id')
                ->on('surveillances')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('last_synced_logs');
    }
};
