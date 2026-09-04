<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrim_deletion_logs', function (Blueprint $table): void {
            $table->json('registration_snapshot')->nullable()->after('entry_date');
        });
    }

    public function down(): void
    {
        Schema::table('pilgrim_deletion_logs', function (Blueprint $table): void {
            $table->dropColumn('registration_snapshot');
        });
    }
};
