<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Platform;
use App\Models\Category;
use App\Models\Skill;
use App\Models\SocialPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Categories (safe to re-run — uses firstOrCreate)
        $categories = collect([
            ['name' => 'Testing & QA', 'description' => 'Skills for writing tests, improving code coverage, and quality assurance workflows.', 'sort_order' => 1],
            ['name' => 'Authentication', 'description' => 'Skills for implementing auth flows, API tokens, OAuth, and session management.', 'sort_order' => 2],
            ['name' => 'Database & Eloquent', 'description' => 'Skills for database design, Eloquent patterns, migrations, and query optimization.', 'sort_order' => 3],
            ['name' => 'API Development', 'description' => 'Skills for building RESTful APIs, API resources, versioning, and documentation.', 'sort_order' => 4],
            ['name' => 'DevOps & Deployment', 'description' => 'Skills for CI/CD, Laravel Cloud deployment, Docker, and infrastructure.', 'sort_order' => 5],
        ])->each(fn (array $data) => Category::firstOrCreate(
            ['slug' => Str::slug($data['name'])],
            [...$data, 'slug' => Str::slug($data['name'])],
        ));

        $this->command->info('Categories ready.');

        // Feature the top 6 most-installed skills for the homepage
        $featured = Skill::query()
            ->orderByDesc('install_count')
            ->limit(6)
            ->update(['is_featured' => true]);

        $this->command->info("Marked {$featured} skills as featured.");

        // Social posts for the community feed (only if empty)
        if (SocialPost::count() === 0) {
            SocialPost::factory()
                ->count(50)
                ->sequence(
                    ['platform' => Platform::X],
                    ['platform' => Platform::Bluesky],
                    ['platform' => Platform::YouTube],
                    ['platform' => Platform::DevTo],
                )
                ->create();

            $this->command->info('Created 50 social posts.');
        } else {
            $this->command->info('Social posts already exist, skipping.');
        }
    }
}
