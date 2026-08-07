<?php

namespace App\Models;

use Database\Factories\MaktabCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaktabCategory extends Model
{
    /** @use HasFactory<MaktabCategoryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'zone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
