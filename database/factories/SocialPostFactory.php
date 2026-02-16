<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Platform;
use App\Models\SocialPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialPost>
 */
class SocialPostFactory extends Factory
{
    protected $model = SocialPost::class;

    public function definition(): array
    {
        $platform = fake()->randomElement(Platform::cases());
        $publishedAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'platform' => $platform,
            'platform_id' => fake()->unique()->numerify('##########'),
            'author_name' => fake()->name(),
            'author_handle' => '@'.fake()->userName(),
            'author_avatar_url' => 'https://i.pravatar.cc/150?u='.fake()->unique()->numberBetween(1000, 99999),
            'content' => $this->generatePostContent($platform),
            'media_url' => null,
            'post_url' => $this->generatePostUrl($platform),
            'engagement_score' => fake()->numberBetween(0, 500),
            'is_featured' => fake()->boolean(10),
            'is_hidden' => false,
            'published_at' => $publishedAt,
            'fetched_at' => now(),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_hidden' => true,
        ]);
    }

    public function forPlatform(Platform $platform): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => $platform,
            'post_url' => $this->generatePostUrl($platform),
            'content' => $this->generatePostContent($platform),
        ]);
    }

    private function generatePostContent(Platform $platform): string
    {
        $topics = [
            'Just shipped a new Laravel skill for Claude Code that handles Eloquent query optimization. The AI now suggests N+1 fixes automatically!',
            'Built a Pest testing skill that generates test scaffolds for your Laravel controllers. Vibe coding at its finest.',
            'Laravel + AI agents is the future. My new skill helps Claude understand Inertia.js patterns and generate Vue components.',
            'Excited about the Laravel skills directory! Finally a place to share reusable AI agent knowledge for PHP developers.',
            'New skill drop: automated Laravel migration generation with AI. Tell Claude what you need, it writes the migration.',
            'The community around Laravel AI skills is growing fast. Love seeing PHP developers embrace AI-first development.',
            'Just published my Sanctum authentication skill. Claude Code now sets up API auth in seconds.',
            'Working on a Laravel deployment skill for Cloud. AI-assisted DevOps is incredibly powerful.',
            'PHP 8.3 features in Laravel — my new skill teaches AI assistants about enums, fibers, and readonly properties.',
            'Laravel Pint + AI = perfectly formatted code every time. Shared my configuration skill on the directory.',
        ];

        return fake()->randomElement($topics);
    }

    private function generatePostUrl(Platform $platform): string
    {
        return match ($platform) {
            Platform::X => 'https://x.com/'.fake()->userName().'/status/'.fake()->numerify('##################'),
            Platform::Bluesky => 'https://bsky.app/profile/'.fake()->userName().'.bsky.social/post/'.fake()->lexify('???????????'),
            Platform::YouTube => 'https://youtube.com/watch?v='.fake()->lexify('???????????'),
            Platform::DevTo => 'https://dev.to/'.fake()->userName().'/'.fake()->slug(6),
        };
    }
}
