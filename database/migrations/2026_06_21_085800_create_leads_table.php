<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_no')->unique();   // e.g. LEAD-2026-00001, auto-generated

            // Contact info
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();

            // Location
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            // What the lead is interested in
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sub_service_id')->nullable()->constrained()->nullOnDelete();

            // Where the lead came from
            $table->foreignId('channel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();

            // Pipeline tracking
            $table->foreignId('lead_status_id')->constrained()->restrictOnDelete();
            $table->foreignId('qualified_status_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            // Conversion link
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('converted_at')->nullable();

            // Deal info
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->decimal('expected_value', 15, 2)->nullable();
            $table->date('expected_close_date')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->string('lost_reason')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('lead_status_id');
            $table->index('assigned_to');
            $table->index('next_follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};