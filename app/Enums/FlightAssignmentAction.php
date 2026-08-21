<?php

namespace App\Enums;

enum FlightAssignmentAction: string
{
    case Assigned = 'assigned';
    case Removed = 'removed';

    public function label(): string
    {
        return match ($this) {
            self::Assigned => 'Assigned',
            self::Removed => 'Removed',
        };
    }
}
