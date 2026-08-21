<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->string('passport_path')->nullable()->after('photo_path');
            $table->string('visa_path')->nullable()->after('passport_path');
            $table->string('ticket_path')->nullable()->after('visa_path');
        });
    }

    public function down(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->dropColumn(['passport_path', 'visa_path', 'ticket_path']);
        });
    }
};
