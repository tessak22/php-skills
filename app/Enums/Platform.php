<?php

declare(strict_types=1);

namespace App\Enums;

enum Platform: string
{
    case X = 'x';
    case Bluesky = 'bluesky';
    case YouTube = 'youtube';
    case DevTo = 'devto';
    case Community = 'community';

    public function label(): string
    {
        return match ($this) {
            self::X => 'X',
            self::Bluesky => 'Bluesky',
            self::YouTube => 'YouTube',
            self::DevTo => 'DEV.to',
            self::Community => 'Community',
        };
    }
}
