<?php

namespace App\Models;

use App\Enums\PackageDuration;
use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'name',
        'price',
        'days',
        'qurbani_included',
        'duration',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'days' => 'integer',
            'qurbani_included' => 'boolean',
            'duration' => PackageDuration::class,
            'is_active' => 'boolean',
        ];
    }
}
