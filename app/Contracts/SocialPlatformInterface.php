<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\Platform;
use Illuminate\Support\Collection;

interface SocialPlatformInterface
{
    /**
     * Fetch posts from the social platform API.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function fetch(): Collection;

    /**
     * Normalize a raw API response into the social_posts schema.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalize(array $data): array;

    /**
     * Determine if a raw post passes the content filter.
     *
     * @param  array<string, mixed>  $data
     */
    public function filter(array $data): bool;

    /**
     * Get the platform enum value for this adapter.
     */
    public function platform(): Platform;
}
