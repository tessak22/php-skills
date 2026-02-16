<?php

declare(strict_types=1);

namespace App\Services\Social;

use App\Contracts\SocialPlatformInterface;
use App\Enums\Platform;
use App\Services\ContentFilterService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DevToAdapter implements SocialPlatformInterface
{
    protected string $baseUrl = 'https://dev.to/api';

    public function __construct(
        protected ContentFilterService $filterService,
    ) {}

    /**
     * Fetch articles from DEV.to API by Laravel/PHP tags.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function fetch(): Collection
    {
        $apiKey = config('services.devto.api_key');

        $headers = [];
        if ($apiKey) {
            $headers['api-key'] = $apiKey;
        }

        $articles = collect();

        // Fetch articles for multiple relevant tags
        $tags = ['laravel', 'php'];

        foreach ($tags as $tag) {
            $response = Http::withHeaders($headers)
                ->retry(3, 100)
                ->get("{$this->baseUrl}/articles", [
                    'tag' => $tag,
                    'per_page' => 25,
                    'state' => 'rising',
                ]);

            if ($response->failed()) {
                Log::error('DEV.to API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'tag' => $tag,
                ]);

                continue;
            }

            $articles = $articles->merge($response->json());
        }

        // Deduplicate by article ID
        return $articles->unique('id')->values()->map(fn (array $article): array => [
            'article' => $article,
            'content' => ($article['title'] ?? '').' '.($article['description'] ?? '').' '.implode(' ', $article['tag_list'] ?? []),
        ]);
    }

    /**
     * Normalize a DEV.to article into the social_posts schema.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalize(array $data): array
    {
        $article = $data['article'];

        $user = $article['user'] ?? [];
        $reactions = (int) ($article['public_reactions_count'] ?? $article['positive_reactions_count'] ?? 0);
        $comments = (int) ($article['comments_count'] ?? 0);
        $engagement = $reactions + ($comments * 3);

        $coverImage = $article['cover_image'] ?? $article['social_image'] ?? null;

        return [
            'platform' => Platform::DevTo,
            'platform_id' => (string) $article['id'],
            'author_name' => $user['name'] ?? 'Unknown',
            'author_handle' => $user['username'] ?? 'unknown',
            'author_avatar_url' => $user['profile_image_90'] ?? $user['profile_image'] ?? null,
            'content' => $article['title'] ?? '',
            'media_url' => $coverImage,
            'post_url' => $article['url'] ?? '',
            'engagement_score' => $engagement,
            'published_at' => Carbon::parse($article['published_at'] ?? now()),
            'fetched_at' => now(),
        ];
    }

    /**
     * Check if a DEV.to article passes the content filter.
     *
     * @param  array<string, mixed>  $data
     */
    public function filter(array $data): bool
    {
        return $this->filterService->passes($data['content'] ?? '');
    }

    /**
     * Get the platform enum for this adapter.
     */
    public function platform(): Platform
    {
        return Platform::DevTo;
    }
}
