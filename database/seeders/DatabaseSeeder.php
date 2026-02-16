<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Platform;
use App\Enums\Role;
use App\Models\Author;
use App\Models\Category;
use App\Models\Skill;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1 admin user
        $admin = User::factory()->create([
            'name' => 'Taylor Otwell',
            'email' => 'admin@laravelskills.dev',
            'role' => Role::Admin,
        ]);

        // 5 categories (deterministic for demo consistency)
        $categories = collect([
            ['name' => 'Testing & QA', 'description' => 'Skills for writing tests, improving code coverage, and quality assurance workflows.', 'sort_order' => 1],
            ['name' => 'Authentication', 'description' => 'Skills for implementing auth flows, API tokens, OAuth, and session management.', 'sort_order' => 2],
            ['name' => 'Database & Eloquent', 'description' => 'Skills for database design, Eloquent patterns, migrations, and query optimization.', 'sort_order' => 3],
            ['name' => 'API Development', 'description' => 'Skills for building RESTful APIs, API resources, versioning, and documentation.', 'sort_order' => 4],
            ['name' => 'DevOps & Deployment', 'description' => 'Skills for CI/CD, Laravel Cloud deployment, Docker, and infrastructure.', 'sort_order' => 5],
        ])->map(fn (array $data) => Category::create([
            ...$data,
            'slug' => Str::slug($data['name']),
        ]));

        // 3 authors (one linked to admin user)
        $authors = collect([
            Author::create([
                'name' => 'Taylor Otwell',
                'slug' => 'taylor-otwell',
                'github_username' => 'taylorotwell',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/463230',
                'bio' => 'Creator of Laravel. Building the future of PHP.',
                'user_id' => $admin->id,
            ]),
            Author::create([
                'name' => 'Nuno Maduro',
                'slug' => 'nuno-maduro',
                'github_username' => 'nunomaduro',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/5457236',
                'bio' => 'Software engineer at Laravel. Creator of Pest, Laravel Pint, and more.',
            ]),
            Author::create([
                'name' => 'Caleb Porzio',
                'slug' => 'caleb-porzio',
                'github_username' => 'calebporzio',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/3670578',
                'bio' => 'Creator of Livewire and Alpine.js. Laravel enthusiast.',
            ]),
        ]);

        // 20 skills distributed across authors and categories
        Skill::factory()
            ->count(20)
            ->sequence(fn ($sequence) => [
                'author_id' => $authors[$sequence->index % 3]->id,
                'category_id' => $categories[$sequence->index % 5]->id,
            ])
            ->create();

        // 50 social posts distributed across platforms
        SocialPost::factory()
            ->count(50)
            ->sequence(
                ['platform' => Platform::X],
                ['platform' => Platform::Bluesky],
                ['platform' => Platform::YouTube],
                ['platform' => Platform::DevTo],
            )
            ->create();
    }
}
