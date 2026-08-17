<?php

namespace App\Enums;

enum FlightDirection: string
{
    case Outbound = 'outbound';
    case Return = 'return';

    public function label(): string
    {
        return match ($this) {
            self::Outbound => 'Departure to Hajj',
            self::Return => 'Return from Hajj',
        };
    }
}
