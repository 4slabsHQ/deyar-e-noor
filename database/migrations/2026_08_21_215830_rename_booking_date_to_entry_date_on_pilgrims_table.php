<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->renameColumn('booking_date', 'entry_date');
        });

        DB::table('users')
            ->whereNotNull('report_preferences')
            ->orderBy('id')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $preferences = json_decode($user->report_preferences, true);

                    if (! is_array($preferences)) {
                        continue;
                    }

                    $updated = false;

                    foreach ($preferences as $reportKey => $columns) {
                        if (! is_array($columns)) {
                            continue;
                        }

                        $preferences[$reportKey] = array_map(
                            fn (mixed $column): mixed => $column === 'booking_date' ? 'entry_date' : $column,
                            $columns,
                        );

                        if ($preferences[$reportKey] !== $columns) {
                            $updated = true;
                        }
                    }

                    if ($updated) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['report_preferences' => json_encode($preferences)]);
                    }
                }
            });
    }

    public function down(): void
    {
        DB::table('users')
            ->whereNotNull('report_preferences')
            ->orderBy('id')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $preferences = json_decode($user->report_preferences, true);

                    if (! is_array($preferences)) {
                        continue;
                    }

                    $updated = false;

                    foreach ($preferences as $reportKey => $columns) {
                        if (! is_array($columns)) {
                            continue;
                        }

                        $preferences[$reportKey] = array_map(
                            fn (mixed $column): mixed => $column === 'entry_date' ? 'booking_date' : $column,
                            $columns,
                        );

                        if ($preferences[$reportKey] !== $columns) {
                            $updated = true;
                        }
                    }

                    if ($updated) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['report_preferences' => json_encode($preferences)]);
                    }
                }
            });

        Schema::table('pilgrims', function (Blueprint $table) {
            $table->renameColumn('entry_date', 'booking_date');
        });
    }
};
