<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->boolean('qurbani_included')->default(false)->after('package_id');
        });
    }

    public function down(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->dropColumn('qurbani_included');
        });
    }
};
