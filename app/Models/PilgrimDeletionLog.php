<?php

namespace App\Models;

use Database\Factories\PilgrimDeletionLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilgrimDeletionLog extends Model
{
    /** @use HasFactory<PilgrimDeletionLogFactory> */
    use HasFactory;

    protected $fillable = [
        'pilgrim_id',
        'deleted_by',
        'deleted_at',
        'hajj_year',
        'full_name',
        'passport_no',
        'family_code',
        'company_id',
        'company_name',
        'package_label',
        'pod_city_name',
        'care_off_name',
        'gender',
        'mobile',
        'entry_date',
        'registration_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
            'hajj_year' => 'integer',
            'entry_date' => 'date',
            'registration_snapshot' => 'array',
        ];
    }

    public function pilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class)->withTrashed();
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
