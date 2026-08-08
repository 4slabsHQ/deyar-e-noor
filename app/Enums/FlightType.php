<?php

namespace App\Enums;

enum FlightType: string
{
    case Direct = 'direct';
    case Indirect = 'indirect';

    public function label(): string
    {
        return match ($this) {
            self::Direct => 'Direct',
            self::Indirect => 'Indirect',
        };
    }
}
