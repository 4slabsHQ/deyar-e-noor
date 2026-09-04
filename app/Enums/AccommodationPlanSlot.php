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
            self::ShiftingBuilding => PropertyCity::MakkahShifting,
        };
    }

    /** @return list<PropertyType> */
    public function propertyTypes(): array
    {
        return match ($this) {
            self::MakkahHotel, self::MadinahHotel => [
                PropertyType::Hotel,
                PropertyType::Building,
            ],
            self::ShiftingBuilding => [
                PropertyType::ShiftingBuilding,
                PropertyType::Building,
            ],
        };
    }

    public function acceptsProperty(PropertyCity $city, PropertyType $type): bool
    {
        return $this->propertyCity() === $city && in_array($type, $this->propertyTypes(), true);
    }
}
