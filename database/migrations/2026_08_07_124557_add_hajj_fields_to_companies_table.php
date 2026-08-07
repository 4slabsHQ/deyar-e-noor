<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('name');
            $table->string('enr_number', 100)->nullable()->after('code');
            $table->string('munazzam_code', 100)->nullable()->after('enr_number');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['code', 'enr_number', 'munazzam_code']);
        });
    }
};
