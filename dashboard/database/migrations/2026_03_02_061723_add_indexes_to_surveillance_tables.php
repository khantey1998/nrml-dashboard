<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveillance_cases', function (Blueprint $table) {
            $table->index(['surveillance_id', 'case_date'], 'idx_surveillance_date');
            $table->index('lab_code', 'idx_lab_code');
            $table->index(['site_province_name', 'case_date'], 'idx_province_date');
        });
    }

    public function down(): void
    {
        Schema::table('surveillance_cases', function (Blueprint $table) {
            $table->dropIndex('idx_surveillance_date');
            $table->dropIndex('idx_lab_code');
            $table->dropIndex('idx_province_date');
        });
    }
};