<?php

namespace App\Enums;

enum AccommodationPlanSlot: string
{
    case MakkahHotel = 'makkah_hotel';
    case MadinahHotel = 'madinah_hotel';
    case ShiftingBuilding = 'shifting_building';

    public function label(): string
    {
        return match ($this) {
            self::MakkahHotel => 'Makkah hotel',
            self::MadinahHotel => 'Madinah hotel',
            self::ShiftingBuilding => 'Shifting building / Sheesha',
        };
    }

    public function propertyCity(): PropertyCity
    {
        return match ($this) {
            self::MakkahHotel => PropertyCity::Makkah,
            self::MadinahHotel => PropertyCity::Madinah,
            self::ShiftingBuilding => PropertyCity::Aziziya,
        };
    }

    public function propertyType(): PropertyType
    {
        return match ($this) {
            self::MakkahHotel, self::MadinahHotel => PropertyType::Hotel,
            self::ShiftingBuilding => PropertyType::ShiftingBuilding,
        };
    }
}
