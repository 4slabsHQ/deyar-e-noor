<?php

namespace App\Models;

use App\Concerns\BelongsToHajjSeason;
use App\Concerns\GuardsDeletionWhenReferenced;
use Database\Factories\CareOffFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareOff extends Model
{
    /** @use HasFactory<CareOffFactory> */
    use BelongsToHajjSeason, GuardsDeletionWhenReferenced, HasFactory, SoftDeletes;

    protected $fillable = [
        'hajj_year',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hajj_year' => 'integer',
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
        return 'care off';
    }
}
