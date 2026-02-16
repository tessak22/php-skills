<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Platform;
use App\Models\SocialPost;
use Illuminate\Database\Seeder;

class SocialPostSeeder extends Seeder
{
    public function run(): void
    {
        // 50 posts across platforms for a realistic-looking feed
        SocialPost::factory()
            ->count(50)
            ->sequence(
                ['platform' => Platform::X],
                ['platform' => Platform::Bluesky],
                ['platform' => Platform::YouTube],
                ['platform' => Platform::DevTo],
            )
            ->create();

        $this->command->info('Created 50 social posts across platforms.');
    }
}
