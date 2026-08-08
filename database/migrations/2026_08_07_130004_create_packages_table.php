<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedSmallInteger('days')->default(0);
            $table->boolean('qurbani_included')->default(false);
            $table->string('duration', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
