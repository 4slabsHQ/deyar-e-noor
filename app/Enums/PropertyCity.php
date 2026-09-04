<?php

namespace App\Enums;

enum PropertyCity: string
{
    case Makkah = 'makkah';
    case Madinah = 'madinah';
    case MakkahShifting = 'makkah_shifting';

    public function label(): string
    {
        return match ($this) {
            self::Makkah => 'Makkah',
            self::Madinah => 'Madinah',
            self::MakkahShifting => 'Makkah shifting',
        };
    }
}
