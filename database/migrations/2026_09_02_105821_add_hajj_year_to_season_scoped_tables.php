<?php

use App\Enums\HajjSeasonStatus;
use App\Models\HajjSeason;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private array $tables = [
        'companies',
        'packages',
        'form_owners',
        'maktab_categories',
        'care_offs',
        'room_types',
        'mehram_relations',
        'waris_relations',
        'flights',
    ];

    public function up(): void
    {
        $backfillYear = $this->resolveBackfillYear();

        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($backfillYear): void {
                $table->unsignedSmallInteger('hajj_year')->default($backfillYear)->after('id');
                $table->index('hajj_year');
            });
        }

        Schema::table('companies', function (Blueprint $table): void {
            $table->dropUnique(['code']);
            $table->unique(['hajj_year', 'code']);
        });

        Schema::table('packages', function (Blueprint $table): void {
            $table->dropUnique(['number']);
            $table->unique(['hajj_year', 'number']);
        });

        Schema::table('form_owners', function (Blueprint $table): void {
            $table->dropUnique(['name']);
            $table->unique(['hajj_year', 'name']);
        });

        Schema::table('maktab_categories', function (Blueprint $table): void {
            $table->dropUnique(['name', 'zone']);
            $table->unique(['hajj_year', 'name', 'zone']);
        });

        Schema::table('care_offs', function (Blueprint $table): void {
            $table->dropUnique(['name']);
            $table->unique(['hajj_year', 'name']);
        });

        Schema::table('room_types', function (Blueprint $table): void {
            $table->dropUnique(['name']);
            $table->unique(['hajj_year', 'name']);
        });

        Schema::table('mehram_relations', function (Blueprint $table): void {
            $table->dropUnique(['name']);
            $table->unique(['hajj_year', 'name']);
        });

        Schema::table('waris_relations', function (Blueprint $table): void {
            $table->dropUnique(['name']);
            $table->unique(['hajj_year', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('waris_relations', function (Blueprint $table): void {
            $table->dropUnique(['hajj_year', 'name']);
            $table->unique(['name']);
        });

        Schema::table('mehram_relations', function (Blueprint $table): void {
            $table->dropUnique(['hajj_year', 'name']);
            $table->unique(['name']);
        });

        Schema::table('room_types', function (Blueprint $table): void {
            $table->dropUnique(['hajj_year', 'name']);
            $table->unique(['name']);
        });

        Schema::table('care_offs', function (Blueprint $table): void {
            $table->dropUnique(['hajj_year', 'name']);
            $table->unique(['name']);
        });

        Schema::table('maktab_categories', function (Blueprint $table): void {
            $table->dropUnique(['hajj_year', 'name', 'zone']);
            $table->unique(['name', 'zone']);
        });

        Schema::table('form_owners', function (Blueprint $table): void {
            $table->dropUnique(['hajj_year', 'name']);
            $table->unique(['name']);
        });

        Schema::table('packages', function (Blueprint $table): void {
            $table->dropUnique(['hajj_year', 'number']);
            $table->unique(['number']);
        });

        Schema::table('companies', function (Blueprint $table): void {
            $table->dropUnique(['hajj_year', 'code']);
            $table->unique(['code']);
        });

        foreach (array_reverse($this->tables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('hajj_year');
            });
        }
    }

    private function resolveBackfillYear(): int
    {
        $activeYear = HajjSeason::query()
            ->where('status', HajjSeasonStatus::Active)
            ->value('year');

        if ($activeYear !== null) {
            return (int) $activeYear;
        }

        $pilgrimYear = DB::table('pilgrims')->max('hajj_year');

        if ($pilgrimYear !== null) {
            return (int) $pilgrimYear;
        }

        $archivedYear = HajjSeason::query()
            ->where('status', HajjSeasonStatus::Archived)
            ->orderByDesc('year')
            ->value('year');

        if ($archivedYear !== null) {
            return (int) $archivedYear;
        }

        return (int) now()->year;
    }
};
