<?php

declare(strict_types=1);

namespace App\Enums;

enum Platform: string
{
    case X = 'x';
    case Bluesky = 'bluesky';
    case YouTube = 'youtube';
    case DevTo = 'devto';

    public function label(): string
    {
        return match ($this) {
            self::X => 'X',
            self::Bluesky => 'Bluesky',
            self::YouTube => 'YouTube',
            self::DevTo => 'DEV.to',
        };
    }
}
