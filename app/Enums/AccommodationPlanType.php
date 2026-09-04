<?php

namespace App\Enums;

enum AccommodationPlanType: string
{
    case Still = 'still';
    case Shifting = 'shifting';

    public function label(): string
    {
        return match ($this) {
            self::Still => 'Still',
            self::Shifting => 'Shifting',
        };
    }

    /** @return list<AccommodationPlanSlot> */
    public function slots(): array
    {
        return match ($this) {
            self::Still => [
                AccommodationPlanSlot::MakkahHotel,
                AccommodationPlanSlot::MadinahHotel,
            ],
            self::Shifting => [
                AccommodationPlanSlot::MakkahHotel,
                AccommodationPlanSlot::ShiftingBuilding,
                AccommodationPlanSlot::MadinahHotel,
            ],
        };
    }
}
