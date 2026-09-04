<?php

namespace App\Enums;

enum RoutePointType: string
{
    case Airport = 'airport';
    case City = 'city';
    case Hajj = 'hajj';

    public function label(): string
    {
        return match ($this) {
            self::Airport => 'Airport',
            self::City => 'City',
            self::Hajj => 'Hajj',
        };
    }
}
