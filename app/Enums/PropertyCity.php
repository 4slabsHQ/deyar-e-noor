<?php

namespace App\Enums;

enum PropertyCity: string
{
    case Makkah = 'makkah';
    case Madinah = 'madinah';
    case Aziziya = 'aziziya';

    public function label(): string
    {
        return match ($this) {
            self::Makkah => 'Makkah',
            self::Madinah => 'Madinah',
            self::Aziziya => 'Aziziya',
        };
    }
}
