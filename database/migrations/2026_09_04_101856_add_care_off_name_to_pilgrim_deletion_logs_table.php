<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrim_deletion_logs', function (Blueprint $table): void {
            $table->string('care_off_name')->nullable()->after('pod_city_name');
        });
    }

    public function down(): void
    {
        Schema::table('pilgrim_deletion_logs', function (Blueprint $table): void {
            $table->dropColumn('care_off_name');
        });
    }
};
