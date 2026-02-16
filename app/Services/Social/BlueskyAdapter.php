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

class BlueskyAdapter implements SocialPlatformInterface
{
    protected string $baseUrl = 'https://public.api.bsky.app';

    public function __construct(
        protected ContentFilterService $filterService,
    ) {}

    /**
     * Fetch posts from Bluesky's public search API.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function fetch(): Collection
    {
        try {
            $response = Http::retry(3, 100)
                ->get("{$this->baseUrl}/xrpc/app.bsky.feed.searchPosts", [
                    'q' => 'laravel ai OR laravel skills OR laravel agent OR php ai',
                    'limit' => 50,
                    'sort' => 'latest',
                ]);

            if ($response->failed()) {
                Log::warning('Bluesky API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return collect();
            }
        } catch (\Throwable $e) {
            Log::warning('Bluesky API unreachable', [
                'error' => $e->getMessage(),
            ]);

            return collect();
        }

        $posts = $response->json('posts', []);

        return collect($posts)->map(fn (array $post): array => [
            'post' => $post,
            'content' => $post['record']['text'] ?? '',
        ]);
    }

    /**
     * Normalize a Bluesky post into the social_posts schema.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalize(array $data): array
    {
        $post = $data['post'];
        $author = $post['author'] ?? [];
        $record = $post['record'] ?? [];

        $embed = $post['embed'] ?? [];
        $mediaUrl = $embed['images'][0]['fullsize'] ?? $embed['thumbnail'] ?? null;

        // Engagement: likes x 1 + reposts x 2 + comments x 3
        $likes = (int) ($post['likeCount'] ?? 0);
        $reposts = (int) ($post['repostCount'] ?? 0);
        $comments = (int) ($post['replyCount'] ?? 0);
        $engagement = ($likes * 1) + ($reposts * 2) + ($comments * 3);

        $handle = $author['handle'] ?? 'unknown';
        $uri = $post['uri'] ?? '';
        $rkey = last(explode('/', $uri));

        return [
            'platform' => Platform::Bluesky,
            'platform_id' => $uri,
            'author_name' => $author['displayName'] ?? $handle,
            'author_handle' => $handle,
            'author_avatar_url' => $author['avatar'] ?? null,
            'content' => $record['text'] ?? '',
            'media_url' => $mediaUrl,
            'post_url' => "https://bsky.app/profile/{$handle}/post/{$rkey}",
            'engagement_score' => $engagement,
            'published_at' => Carbon::parse($record['createdAt'] ?? now()),
            'fetched_at' => now(),
        ];
    }

    /**
     * Check if a Bluesky post passes the content filter.
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
        return Platform::Bluesky;
    }
}
