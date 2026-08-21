<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use GuardsDeletionWhenReferenced, HasFactory;

    protected $fillable = [
        'name', 'iso2', 'iso3', 'phone_code', 'flag', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function airlines(): HasMany
    {
        return $this->hasMany(Airline::class);
    }

    /** @return array<string, string> */
    public function referencedByRelations(): array
    {
        return [
            'cities' => 'cities',
            'airlines' => 'airlines',
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'country';
    }
}
