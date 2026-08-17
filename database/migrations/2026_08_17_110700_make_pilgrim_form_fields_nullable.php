<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->dropForeign(['form_owner_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['maktab_category_id']);
            $table->dropForeign(['package_id']);
            $table->dropForeign(['care_off_id']);
            $table->dropForeign(['pod_city_id']);
            $table->dropForeign(['room_type_id']);
            $table->dropForeign(['mehram_relation_id']);
            $table->dropForeign(['waris_relation_id']);
        });

        Schema::table('pilgrims', function (Blueprint $table) {
            $table->unsignedSmallInteger('hajj_year')->nullable()->change();
            $table->date('booking_date')->nullable()->change();
            $table->unsignedBigInteger('form_owner_id')->nullable()->change();
            $table->unsignedBigInteger('company_id')->nullable()->change();
            $table->unsignedBigInteger('maktab_category_id')->nullable()->change();
            $table->unsignedBigInteger('package_id')->nullable()->change();
            $table->unsignedBigInteger('care_off_id')->nullable()->change();
            $table->unsignedBigInteger('pod_city_id')->nullable()->change();
            $table->unsignedBigInteger('room_type_id')->nullable()->change();
            $table->string('gender', 10)->nullable()->change();
            $table->string('surname')->nullable()->change();
            $table->string('given_name')->nullable()->change();
            $table->string('father_husband_name')->nullable()->change();
            $table->string('full_name')->nullable()->change();
            $table->string('passport_no', 20)->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('birth_place')->nullable()->change();
            $table->date('passport_expiry')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('mobile', 20)->nullable()->change();
            $table->string('cnic', 20)->nullable()->change();
            $table->string('blood_group', 5)->nullable()->change();
            $table->string('mehram_name')->nullable()->change();
            $table->unsignedBigInteger('mehram_relation_id')->nullable()->change();
            $table->string('waris_name')->nullable()->change();
            $table->string('waris_cnic', 20)->nullable()->change();
            $table->unsignedBigInteger('waris_relation_id')->nullable()->change();
            $table->string('waris_mobile', 20)->nullable()->change();
            $table->string('family_code', 50)->nullable()->change();
            $table->unsignedInteger('family_number')->nullable()->change();
            $table->string('family_member_suffix', 2)->nullable()->change();
            $table->unsignedTinyInteger('age')->nullable()->change();
        });

        Schema::table('pilgrims', function (Blueprint $table) {
            $table->foreign('form_owner_id')->references('id')->on('form_owners')->nullOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('maktab_category_id')->references('id')->on('maktab_categories')->nullOnDelete();
            $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
            $table->foreign('care_off_id')->references('id')->on('care_offs')->nullOnDelete();
            $table->foreign('pod_city_id')->references('id')->on('cities')->nullOnDelete();
            $table->foreign('room_type_id')->references('id')->on('room_types')->nullOnDelete();
            $table->foreign('mehram_relation_id')->references('id')->on('mehram_relations')->nullOnDelete();
            $table->foreign('waris_relation_id')->references('id')->on('waris_relations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->dropForeign(['form_owner_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['maktab_category_id']);
            $table->dropForeign(['package_id']);
            $table->dropForeign(['care_off_id']);
            $table->dropForeign(['pod_city_id']);
            $table->dropForeign(['room_type_id']);
            $table->dropForeign(['mehram_relation_id']);
            $table->dropForeign(['waris_relation_id']);
        });

        Schema::table('pilgrims', function (Blueprint $table) {
            $table->unsignedSmallInteger('hajj_year')->nullable(false)->change();
            $table->date('booking_date')->nullable(false)->change();
            $table->unsignedBigInteger('form_owner_id')->nullable(false)->change();
            $table->unsignedBigInteger('company_id')->nullable(false)->change();
            $table->unsignedBigInteger('maktab_category_id')->nullable(false)->change();
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
            $table->unsignedBigInteger('care_off_id')->nullable(false)->change();
            $table->unsignedBigInteger('pod_city_id')->nullable(false)->change();
            $table->unsignedBigInteger('room_type_id')->nullable(false)->change();
            $table->string('gender', 10)->nullable(false)->change();
            $table->string('surname')->nullable(false)->change();
            $table->string('given_name')->nullable(false)->change();
            $table->string('father_husband_name')->nullable(false)->change();
            $table->string('full_name')->nullable(false)->change();
            $table->string('passport_no', 20)->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('birth_place')->nullable(false)->change();
            $table->date('passport_expiry')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('mobile', 20)->nullable(false)->change();
            $table->string('cnic', 20)->nullable(false)->change();
            $table->string('blood_group', 5)->nullable(false)->change();
            $table->string('mehram_name')->nullable(false)->change();
            $table->unsignedBigInteger('mehram_relation_id')->nullable(false)->change();
            $table->string('waris_name')->nullable(false)->change();
            $table->string('waris_cnic', 20)->nullable(false)->change();
            $table->unsignedBigInteger('waris_relation_id')->nullable(false)->change();
            $table->string('waris_mobile', 20)->nullable(false)->change();
            $table->string('family_code', 50)->nullable(false)->change();
            $table->unsignedInteger('family_number')->nullable(false)->change();
            $table->string('family_member_suffix', 2)->nullable(false)->change();
            $table->unsignedTinyInteger('age')->nullable(false)->change();
        });

        Schema::table('pilgrims', function (Blueprint $table) {
            $table->foreign('form_owner_id')->references('id')->on('form_owners')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('maktab_category_id')->references('id')->on('maktab_categories')->cascadeOnDelete();
            $table->foreign('package_id')->references('id')->on('packages')->cascadeOnDelete();
            $table->foreign('care_off_id')->references('id')->on('care_offs')->cascadeOnDelete();
            $table->foreign('pod_city_id')->references('id')->on('cities')->cascadeOnDelete();
            $table->foreign('room_type_id')->references('id')->on('room_types')->cascadeOnDelete();
            $table->foreign('mehram_relation_id')->references('id')->on('mehram_relations')->cascadeOnDelete();
            $table->foreign('waris_relation_id')->references('id')->on('waris_relations')->cascadeOnDelete();
        });
    }
};
