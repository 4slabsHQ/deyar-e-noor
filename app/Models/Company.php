<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'legal_name',
        'enr_number',
        'munazzam_code',
        'quota',
        'registration_number',
        'tax_number',
        'email',
        'phone',
        'website',
        'logo',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'currency',
        'timezone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'quota' => 'integer',
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
        return 'company';
    }

    public function registeredPilgrimCountForYear(int $hajjYear, ?int $excludingPilgrimId = null): int
    {
        return $this->pilgrims()
            ->where('hajj_year', $hajjYear)
            ->when($excludingPilgrimId, fn ($query) => $query->where('id', '!=', $excludingPilgrimId))
            ->count();
    }

    public function hasQuotaForYear(int $hajjYear, ?int $excludingPilgrimId = null): bool
    {
        if ($this->quota === null) {
            return true;
        }

        return $this->registeredPilgrimCountForYear($hajjYear, $excludingPilgrimId) < $this->quota;
    }

    public function remainingQuotaForYear(int $hajjYear, ?int $excludingPilgrimId = null): ?int
    {
        if ($this->quota === null) {
            return null;
        }

        return max(0, $this->quota - $this->registeredPilgrimCountForYear($hajjYear, $excludingPilgrimId));
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
