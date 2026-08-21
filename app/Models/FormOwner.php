<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use Database\Factories\FormOwnerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormOwner extends Model
{
    /** @use HasFactory<FormOwnerFactory> */
    use GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'limit' => 'integer',
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
        return 'form owner';
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
}
