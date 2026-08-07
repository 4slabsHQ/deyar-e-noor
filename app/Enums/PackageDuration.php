<?php

namespace App\Enums;

enum PackageDuration: string
{
    case Long = 'long';
    case Short = 'short';

    public function label(): string
    {
        return match ($this) {
            self::Long => 'Long',
            self::Short => 'Short',
        };
    }
}
