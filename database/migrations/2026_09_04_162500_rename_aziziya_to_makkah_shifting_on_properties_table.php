<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('properties')
            ->where('city', 'aziziya')
            ->update(['city' => 'makkah_shifting']);
    }

    public function down(): void
    {
        DB::table('properties')
            ->where('city', 'makkah_shifting')
            ->update(['city' => 'aziziya']);
    }
};
