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
        Schema::create('case_lab_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lab_code');
            $table->integer('is_positive');
            $table->string('pathogen_name');
            $table->string('subtype')->nullable();
            $table->string('indicator')->nullable();

            $table->index('lab_code');
            $table->index('pathogen_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_lab_results');
    }
};
