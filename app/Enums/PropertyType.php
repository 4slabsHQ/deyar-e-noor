<?php

namespace App\Enums;

enum PropertyType: string
{
    case Hotel = 'hotel';
    case ShiftingBuilding = 'shifting_building';

    public function label(): string
    {
        return match ($this) {
            self::Hotel => 'Hotel',
            self::ShiftingBuilding => 'Shifting building / Sheesha',
        };
    }
}
