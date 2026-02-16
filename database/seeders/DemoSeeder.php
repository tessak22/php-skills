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
        collect([
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
            $this->seedSocialPosts();
            $this->command->info('Created social posts.');
        } else {
            $this->command->info('Social posts already exist, skipping.');
        }
    }

    private function seedSocialPosts(): void
    {
        $posts = [
            ['platform' => Platform::X, 'author_name' => 'Taylor Otwell', 'author_handle' => '@taylorotwell', 'author_avatar_url' => 'https://avatars.githubusercontent.com/taylorotwell', 'content' => 'Just shipped a new Laravel skill for Claude Code that handles Eloquent query optimization. The AI now suggests N+1 fixes automatically!', 'post_url' => 'https://x.com/taylorotwell/status/1893456789012345678', 'engagement_score' => 482],
            ['platform' => Platform::Bluesky, 'author_name' => 'Nuno Maduro', 'author_handle' => '@nunomaduro.bsky.social', 'author_avatar_url' => 'https://avatars.githubusercontent.com/nunomaduro', 'content' => 'Built a Pest testing skill that generates test scaffolds for your Laravel controllers. Vibe coding at its finest.', 'post_url' => 'https://bsky.app/profile/nunomaduro.bsky.social/post/abc123def', 'engagement_score' => 356],
            ['platform' => Platform::YouTube, 'author_name' => 'Laracasts', 'author_handle' => '@laracasts', 'author_avatar_url' => 'https://avatars.githubusercontent.com/laracasts', 'content' => 'Laravel + AI agents is the future. My new skill helps Claude understand Inertia.js patterns and generate Vue components.', 'post_url' => 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'engagement_score' => 291],
            ['platform' => Platform::DevTo, 'author_name' => 'Freek Van der Herten', 'author_handle' => '@freekmurze', 'author_avatar_url' => 'https://avatars.githubusercontent.com/freekmurze', 'content' => 'Excited about the Laravel skills directory! Finally a place to share reusable AI agent knowledge for PHP developers.', 'post_url' => 'https://dev.to/freekmurze/laravel-ai-skills', 'engagement_score' => 245],
            ['platform' => Platform::X, 'author_name' => 'Caleb Porzio', 'author_handle' => '@caaborozio', 'author_avatar_url' => 'https://avatars.githubusercontent.com/calebporzio', 'content' => 'New skill drop: automated Laravel migration generation with AI. Tell Claude what you need, it writes the migration.', 'post_url' => 'https://x.com/calebporzio/status/1893456789012345679', 'engagement_score' => 389],
            ['platform' => Platform::Bluesky, 'author_name' => 'Mohamed Said', 'author_handle' => '@themsaid.bsky.social', 'author_avatar_url' => 'https://avatars.githubusercontent.com/themsaid', 'content' => 'The community around Laravel AI skills is growing fast. Love seeing PHP developers embrace AI-first development.', 'post_url' => 'https://bsky.app/profile/themsaid.bsky.social/post/xyz789ghi', 'engagement_score' => 178],
            ['platform' => Platform::YouTube, 'author_name' => 'Laravel News', 'author_handle' => '@laravelnews', 'author_avatar_url' => 'https://avatars.githubusercontent.com/laravelnews', 'content' => 'Just published my Sanctum authentication skill. Claude Code now sets up API auth in seconds.', 'post_url' => 'https://youtube.com/watch?v=abc123xyz', 'engagement_score' => 312],
            ['platform' => Platform::DevTo, 'author_name' => 'Steve McDougall', 'author_handle' => '@juststeveking', 'author_avatar_url' => 'https://avatars.githubusercontent.com/JustSteveKing', 'content' => 'Working on a Laravel deployment skill for Cloud. AI-assisted DevOps is incredibly powerful.', 'post_url' => 'https://dev.to/juststeveking/laravel-cloud-skill', 'engagement_score' => 198],
            ['platform' => Platform::X, 'author_name' => 'Jess Archer', 'author_handle' => '@jessarchercodes', 'author_avatar_url' => 'https://avatars.githubusercontent.com/jessarcher', 'content' => 'PHP 8.3 features in Laravel — my new skill teaches AI assistants about enums, fibers, and readonly properties.', 'post_url' => 'https://x.com/jessarchercodes/status/1893456789012345680', 'engagement_score' => 267],
            ['platform' => Platform::Bluesky, 'author_name' => 'Tim MacDonald', 'author_handle' => '@timacdonald.bsky.social', 'author_avatar_url' => 'https://avatars.githubusercontent.com/timacdonald', 'content' => 'Laravel Pint + AI = perfectly formatted code every time. Shared my configuration skill on the directory.', 'post_url' => 'https://bsky.app/profile/timacdonald.bsky.social/post/pqr456stu', 'engagement_score' => 156],
            ['platform' => Platform::X, 'author_name' => 'Aaron Francis', 'author_handle' => '@aarondfrancis', 'author_avatar_url' => 'https://avatars.githubusercontent.com/aarondfrancis', 'content' => 'Database indexing skill for Laravel just dropped. Your AI now knows when and how to add the right indexes.', 'post_url' => 'https://x.com/aarondfrancis/status/1893456789012345681', 'engagement_score' => 445],
            ['platform' => Platform::YouTube, 'author_name' => 'Christoph Rumpel', 'author_handle' => '@christophrumpel', 'author_avatar_url' => 'https://avatars.githubusercontent.com/christophrumpel', 'content' => 'Created a skill that teaches Claude how to write proper Laravel queued jobs. Retry logic, backoff, the works.', 'post_url' => 'https://youtube.com/watch?v=def456ghi', 'engagement_score' => 203],
            ['platform' => Platform::DevTo, 'author_name' => 'Povilas Korop', 'author_handle' => '@laaboraveldaily', 'author_avatar_url' => 'https://avatars.githubusercontent.com/LaravelDaily', 'content' => 'Published a comprehensive Eloquent relationships skill. AI agents finally understand polymorphic relations properly.', 'post_url' => 'https://dev.to/laraveldaily/eloquent-relationships-skill', 'engagement_score' => 334],
            ['platform' => Platform::X, 'author_name' => 'Marcel Pociot', 'author_handle' => '@marcelpociot', 'author_avatar_url' => 'https://avatars.githubusercontent.com/mpociot', 'content' => 'The Laravel skills ecosystem is what I always wanted. AI agents that actually understand our framework conventions.', 'post_url' => 'https://x.com/marcelpociot/status/1893456789012345682', 'engagement_score' => 378],
            ['platform' => Platform::Bluesky, 'author_name' => 'Luke Downing', 'author_handle' => '@lukedowning.bsky.social', 'author_avatar_url' => 'https://avatars.githubusercontent.com/lukeraymonddowning', 'content' => 'Livewire + Alpine.js skill is live! Claude now scaffolds reactive components that follow all the best practices.', 'post_url' => 'https://bsky.app/profile/lukedowning.bsky.social/post/mno789vwx', 'engagement_score' => 223],
            ['platform' => Platform::X, 'author_name' => 'Dries Vints', 'author_handle' => '@driesvints', 'author_avatar_url' => 'https://avatars.githubusercontent.com/driesvints', 'content' => 'Cashier billing skill just published. AI can now set up Stripe subscriptions with proper webhook handling.', 'post_url' => 'https://x.com/driesvints/status/1893456789012345683', 'engagement_score' => 289],
            ['platform' => Platform::DevTo, 'author_name' => 'Ash Allen', 'author_handle' => '@ashallen', 'author_avatar_url' => 'https://avatars.githubusercontent.com/ash-jc-allen', 'content' => 'API versioning skill for Laravel is out. Finally an AI that knows how to properly version your REST endpoints.', 'post_url' => 'https://dev.to/ashallen/api-versioning-laravel', 'engagement_score' => 167],
            ['platform' => Platform::YouTube, 'author_name' => 'Matt Stauffer', 'author_handle' => '@stauffermatt', 'author_avatar_url' => 'https://avatars.githubusercontent.com/mattstauffer', 'content' => 'Deep dive into creating your own Laravel skills for AI agents. The future of developer tooling is here.', 'post_url' => 'https://youtube.com/watch?v=ghi789jkl', 'engagement_score' => 412],
            ['platform' => Platform::Bluesky, 'author_name' => 'Jason McCreary', 'author_handle' => '@jasonmccreary.bsky.social', 'author_avatar_url' => 'https://avatars.githubusercontent.com/jasonmccreary', 'content' => 'Laravel Shift + AI skills = unstoppable upgrade automation. Just published my upgrade patterns skill.', 'post_url' => 'https://bsky.app/profile/jasonmccreary.bsky.social/post/rst012uvw', 'engagement_score' => 234],
            ['platform' => Platform::X, 'author_name' => 'Spatie', 'author_handle' => '@spaboratie_be', 'author_avatar_url' => 'https://avatars.githubusercontent.com/spatie', 'content' => 'Our media library skill teaches AI agents how to handle file uploads, conversions, and responsive images in Laravel.', 'post_url' => 'https://x.com/spatie_be/status/1893456789012345684', 'engagement_score' => 356],
        ];

        $now = now();

        foreach ($posts as $i => $post) {
            SocialPost::create([
                ...$post,
                'platform_id' => 'demo-'.($i + 1),
                'media_url' => null,
                'is_featured' => $i < 3,
                'is_hidden' => false,
                'published_at' => $now->copy()->subHours($i * 4),
                'fetched_at' => $now,
            ]);
        }
    }
}
