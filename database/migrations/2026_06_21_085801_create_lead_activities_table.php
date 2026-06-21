<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();

            $table->enum('activity_type', [
                'call', 'email', 'whatsapp', 'meeting', 'note', 'status_change', 'follow_up',
            ])->default('note');

            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('outcome')->nullable();

            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};