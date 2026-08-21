<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flight_pilgrim', function (Blueprint $table) {
            $table->foreignId('assigned_by')
                ->nullable()
                ->after('pilgrim_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('flight_pilgrim', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_by');
        });
    }
};
