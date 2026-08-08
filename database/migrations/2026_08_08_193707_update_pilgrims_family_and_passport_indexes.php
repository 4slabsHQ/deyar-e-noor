<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            if (! Schema::hasIndex('pilgrims', 'pilgrims_hajj_year_passport_no_unique')) {
                if (Schema::hasIndex('pilgrims', 'pilgrims_passport_no_unique')) {
                    $table->dropUnique(['passport_no']);
                }

                $table->unique(['hajj_year', 'passport_no']);
            }

            if (! Schema::hasIndex('pilgrims', 'pilgrims_company_hajj_year_family_index')) {
                $table->index(['company_id', 'hajj_year', 'family_number'], 'pilgrims_company_hajj_year_family_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            if (Schema::hasIndex('pilgrims', 'pilgrims_company_hajj_year_family_index')) {
                $table->dropIndex('pilgrims_company_hajj_year_family_index');
            }

            if (Schema::hasIndex('pilgrims', 'pilgrims_hajj_year_passport_no_unique')) {
                $table->dropUnique(['hajj_year', 'passport_no']);
                $table->unique('passport_no');
            }
        });
    }
};
