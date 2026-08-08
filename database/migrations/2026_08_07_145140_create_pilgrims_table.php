<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilgrims', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('hajj_year');
            $table->date('booking_date');
            $table->foreignId('form_owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maktab_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('care_off_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pod_city_id')->constrained('cities')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('gender', 10);
            $table->string('surname');
            $table->string('given_name');
            $table->string('father_husband_name');
            $table->string('full_name');
            $table->string('passport_no', 20)->unique();
            $table->date('date_of_birth');
            $table->string('birth_place');
            $table->date('passport_expiry');
            $table->text('address');
            $table->string('mobile', 20);
            $table->string('cnic', 20);
            $table->string('blood_group', 5);
            $table->string('mehram_name');
            $table->foreignId('mehram_relation_id')->constrained()->cascadeOnDelete();
            $table->string('waris_name');
            $table->string('waris_cnic', 20);
            $table->foreignId('waris_relation_id')->constrained()->cascadeOnDelete();
            $table->string('waris_mobile', 20);
            $table->string('family_code', 50);
            $table->unsignedInteger('family_number');
            $table->string('family_member_suffix', 2);
            $table->unsignedTinyInteger('age');
            $table->string('photo_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'family_number']);
            $table->index('family_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilgrims');
    }
};
