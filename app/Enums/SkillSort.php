<?php

declare(strict_types=1);

namespace App\Enums;

enum SkillSort: string
{
    case Newest = 'newest';
    case Installs = 'installs';
}
