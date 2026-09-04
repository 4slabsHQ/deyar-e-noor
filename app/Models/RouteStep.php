<?php

namespace App\Models;

use App\Enums\RoutePointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStep extends Model
{
    protected $fillable = [
        'route_id',
        'sequence',
        'point_type',
        'airport_id',
        'city_id',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'point_type' => RoutePointType::class,
        ];
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function label(): string
    {
        return match ($this->point_type) {
            RoutePointType::Airport => $this->airport
                ? sprintf('%s (%s)', $this->airport->name, $this->airport->code)
                : '—',
            RoutePointType::City => $this->city?->name ?? '—',
            RoutePointType::Hajj => 'Hajj',
        };
    }
}
