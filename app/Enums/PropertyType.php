<?php

namespace App\Enums;

enum PropertyType: string
{
    case Hotel = 'hotel';
    case ShiftingBuilding = 'shifting_building';
    case Building = 'building';

    public function label(): string
    {
        return match ($this) {
            self::Hotel => 'Hotel',
            self::ShiftingBuilding => 'Shifting building / Sheesha',
            self::Building => 'Building',
        };
    }
}
