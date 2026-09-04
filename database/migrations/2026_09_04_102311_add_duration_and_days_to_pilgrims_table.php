<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table): void {
            $table->unsignedSmallInteger('days')->nullable()->after('qurbani_included');
            $table->string('duration', 10)->nullable()->after('days');
        });

        $pilgrims = DB::table('pilgrims')
            ->whereNull('days')
            ->whereNotNull('package_id')
            ->get(['id', 'package_id']);

        foreach ($pilgrims as $pilgrim) {
            $package = DB::table('packages')
                ->where('id', $pilgrim->package_id)
                ->first(['days', 'duration']);

            if ($package === null) {
                continue;
            }

            DB::table('pilgrims')
                ->where('id', $pilgrim->id)
                ->update([
                    'days' => $package->days,
                    'duration' => $package->duration,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('pilgrims', function (Blueprint $table): void {
            $table->dropColumn(['days', 'duration']);
        });
    }
};
