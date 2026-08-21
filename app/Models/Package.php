<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use App\Enums\PackageDuration;
use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'name',
        'price',
        'days',
        'qurbani_included',
        'duration',
        'limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'days' => 'integer',
            'limit' => 'integer',
            'qurbani_included' => 'boolean',
            'duration' => PackageDuration::class,
            'is_active' => 'boolean',
        ];
    }

    public function pilgrims(): HasMany
    {
        return $this->hasMany(Pilgrim::class);
    }

    /** @return array<string, string> */
    public function referencedByRelations(): array
    {
        return [
            'pilgrims' => 'Hajj registrations',
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'package';
    }

    public function registeredPilgrimCountForYear(int $hajjYear, ?int $excludingPilgrimId = null): int
    {
        return $this->pilgrims()
            ->where('hajj_year', $hajjYear)
            ->when($excludingPilgrimId, fn ($query) => $query->where('id', '!=', $excludingPilgrimId))
            ->count();
    }

    public function hasLimitForYear(int $hajjYear, ?int $excludingPilgrimId = null): bool
    {
        if ($this->limit === null) {
            return true;
        }

        return $this->registeredPilgrimCountForYear($hajjYear, $excludingPilgrimId) < $this->limit;
    }

    public function registrationOptionLabel(): string
    {
        return sprintf(
            '%s — %s | %s | %d days | %s | %s',
            $this->number,
            $this->name,
            number_format((float) $this->price, 2),
            $this->days,
            $this->duration->label(),
            $this->qurbani_included ? 'Qurbani included' : 'No qurbani',
        );
    }
}
