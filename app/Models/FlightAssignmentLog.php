<?php

namespace App\Models;

use App\Enums\FlightAssignmentAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightAssignmentLog extends Model
{
    protected $fillable = [
        'flight_id',
        'pilgrim_id',
        'action',
        'performed_by',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'action' => FlightAssignmentAction::class,
            'performed_at' => 'datetime',
        ];
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function pilgrim(): BelongsTo
    {
        return $this->belongsTo(Pilgrim::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
