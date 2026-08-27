<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilgrim_deletion_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('deleted_at');
            $table->unsignedSmallInteger('hajj_year')->nullable();
            $table->string('full_name')->nullable();
            $table->string('passport_no')->nullable();
            $table->string('family_code')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name')->nullable();
            $table->string('package_label')->nullable();
            $table->string('pod_city_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('mobile')->nullable();
            $table->date('entry_date')->nullable();
            $table->timestamps();

            $table->index(['hajj_year', 'deleted_at']);
            $table->index('company_id');
            $table->index('deleted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilgrim_deletion_logs');
    }
};
