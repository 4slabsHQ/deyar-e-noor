<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('route_steps', function (Blueprint $table) {
            $table->foreignId('airport_id')->nullable()->after('point_type')->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('airport_id')->constrained()->nullOnDelete();
        });

        Schema::table('route_steps', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }

    public function down(): void
    {
        Schema::table('route_steps', function (Blueprint $table) {
            $table->string('location')->after('point_type');
        });

        Schema::table('route_steps', function (Blueprint $table) {
            $table->dropConstrainedForeignId('airport_id');
            $table->dropConstrainedForeignId('city_id');
        });
    }
};
