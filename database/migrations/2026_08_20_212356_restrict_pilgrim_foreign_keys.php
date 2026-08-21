<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<int, string> */
    private array $foreignKeys = [
        'form_owner_id',
        'company_id',
        'maktab_category_id',
        'package_id',
        'care_off_id',
        'pod_city_id',
        'room_type_id',
        'mehram_relation_id',
        'waris_relation_id',
    ];

    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table): void {
            foreach ($this->foreignKeys as $column) {
                $table->dropForeign([$column]);
            }
        });

        Schema::table('pilgrims', function (Blueprint $table): void {
            $table->foreign('form_owner_id')->references('id')->on('form_owners')->restrictOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->restrictOnDelete();
            $table->foreign('maktab_category_id')->references('id')->on('maktab_categories')->restrictOnDelete();
            $table->foreign('package_id')->references('id')->on('packages')->restrictOnDelete();
            $table->foreign('care_off_id')->references('id')->on('care_offs')->restrictOnDelete();
            $table->foreign('pod_city_id')->references('id')->on('cities')->restrictOnDelete();
            $table->foreign('room_type_id')->references('id')->on('room_types')->restrictOnDelete();
            $table->foreign('mehram_relation_id')->references('id')->on('mehram_relations')->restrictOnDelete();
            $table->foreign('waris_relation_id')->references('id')->on('waris_relations')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pilgrims', function (Blueprint $table): void {
            foreach ($this->foreignKeys as $column) {
                $table->dropForeign([$column]);
            }
        });

        Schema::table('pilgrims', function (Blueprint $table): void {
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
