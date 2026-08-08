<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_type', 20);

            $table->foreignId('departure_city_id')->constrained('cities');
            $table->foreignId('departure_airport_id')->constrained('airports');
            $table->foreignId('departure_airline_id')->constrained('airlines');
            $table->string('departure_flight_no', 20);
            $table->date('departure_date');
            $table->time('departure_time');

            $table->foreignId('via_city_id')->nullable()->constrained('cities');
            $table->foreignId('via_airport_id')->nullable()->constrained('airports');
            $table->date('via_arrival_date')->nullable();
            $table->time('via_arrival_time')->nullable();
            $table->foreignId('via_airline_id')->nullable()->constrained('airlines');
            $table->string('via_departure_flight_no', 20)->nullable();
            $table->date('via_departure_date')->nullable();
            $table->time('via_departure_time')->nullable();
            $table->unsignedInteger('via_total_stay_minutes')->nullable();

            $table->foreignId('arrival_city_id')->constrained('cities');
            $table->foreignId('arrival_airport_id')->constrained('airports');
            $table->date('arrival_date');
            $table->time('arrival_time');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
