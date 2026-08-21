<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            if (! Schema::hasIndex('pilgrims', 'pilgrims_hajj_year_cnic_unique')) {
                $table->unique(['hajj_year', 'cnic'], 'pilgrims_hajj_year_cnic_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            if (Schema::hasIndex('pilgrims', 'pilgrims_hajj_year_cnic_unique')) {
                $table->dropUnique('pilgrims_hajj_year_cnic_unique');
            }
        });
    }
};
