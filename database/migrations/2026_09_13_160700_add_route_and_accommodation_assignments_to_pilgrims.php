<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            if (! Schema::hasColumn('pilgrims', 'route_id')) {
                $table->foreignId('route_id')
                    ->nullable()
                    ->after('package_id')
                    ->constrained()
                    ->nullOnDelete();
            }
        });

        Schema::create('pilgrim_accommodation_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained()->cascadeOnDelete();
            $table->string('slot', 32);
            $table->foreignId('property_akad_id')->nullable()->constrained()->nullOnDelete();
            $table->string('room_number', 50)->nullable();
            $table->timestamps();

            $table->unique(['pilgrim_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilgrim_accommodation_slots');

        Schema::table('pilgrims', function (Blueprint $table) {
            if (Schema::hasColumn('pilgrims', 'route_id')) {
                $table->dropConstrainedForeignId('route_id');
            }
        });
    }
};
