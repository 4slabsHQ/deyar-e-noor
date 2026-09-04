<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('hajj_year');
            $table->string('name');
            $table->string('city', 32);
            $table->string('type', 32);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['hajj_year', 'name', 'city']);
            $table->index(['hajj_year', 'city', 'type']);
        });

        Schema::create('property_akads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('akad_number', 100);
            $table->string('label')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'akad_number']);
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('hajj_year');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['hajj_year', 'name']);
        });

        Schema::create('route_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sequence');
            $table->string('point_type', 32);
            $table->string('location');
            $table->timestamps();

            $table->unique(['route_id', 'sequence']);
        });

        Schema::create('accommodation_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('hajj_year');
            $table->string('name');
            $table->string('type', 32);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['hajj_year', 'name']);
        });

        Schema::create('accommodation_plan_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_plan_id')->constrained()->cascadeOnDelete();
            $table->string('slot', 32);
            $table->foreignId('property_id')->constrained();
            $table->foreignId('property_akad_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('sequence');
            $table->timestamps();

            $table->unique(['accommodation_plan_id', 'slot']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->foreignId('accommodation_plan_id')->nullable()->after('duration')->constrained()->nullOnDelete();
            $table->foreignId('route_id')->nullable()->after('accommodation_plan_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('route_id');
            $table->dropConstrainedForeignId('accommodation_plan_id');
        });

        Schema::dropIfExists('accommodation_plan_slots');
        Schema::dropIfExists('accommodation_plans');
        Schema::dropIfExists('route_steps');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('property_akads');
        Schema::dropIfExists('properties');
    }
};
