<?php

namespace App\Models;

use App\Enums\HajjSeasonStatus;
use Database\Factories\HajjSeasonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HajjSeason extends Model
{
    /** @use HasFactory<HajjSeasonFactory> */
    use HasFactory;

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
}
