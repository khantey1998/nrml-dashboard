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
        Schema::create('surveillance_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lab_code');
            $table->integer('is_newcase');
            $table->string('sentinel_site_name');
            $table->string('site_province_name');
            $table->unsignedInteger('surveillance_id');
            $table->integer('year_data');
            $table->integer('week_data');
            $table->integer('patient_age_inday');
            $table->string('patient_sex');
            $table->tinyInteger('is_alive');
            $table->string('patient_privince');

            $table->foreign('surveillance_id')
                ->references('id')
                ->on('surveillances')
                ->onDelete('cascade');

            $table->index('lab_code');
            $table->index('year_data');
            $table->index('week_data');
            $table->index('site_province_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveillance_cases');
    }
};
