<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_pilgrim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pilgrim_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['flight_id', 'pilgrim_id']);
            $table->index('pilgrim_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_pilgrim');
    }
};
