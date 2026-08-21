<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use App\Enums\HajjSeasonStatus;
use Database\Factories\HajjSeasonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HajjSeason extends Model
{
    /** @use HasFactory<HajjSeasonFactory> */
    use GuardsDeletionWhenReferenced, HasFactory;

    protected $fillable = [
        'year',
        'status',
        'activated_by',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'status' => HajjSeasonStatus::class,
            'activated_at' => 'datetime',
        ];
    }

    public function activator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function isActive(): bool
    {
        return $this->status === HajjSeasonStatus::Active;
    }

    /** @return array<int, callable(HajjSeason): ?string> */
    public function referencedByChecks(): array
    {
        return [
            function (HajjSeason $season): ?string {
                $count = Pilgrim::query()->where('hajj_year', $season->year)->count();

                if ($count === 0) {
                    return null;
                }

                return sprintf(
                    'Cannot delete this Hajj season because it is linked to %d Hajj registration(s).',
                    $count,
                );
            },
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'Hajj season';
    }
}
