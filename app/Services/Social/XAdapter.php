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

class XAdapter implements SocialPlatformInterface
{
    protected string $baseUrl = 'https://api.twitter.com/2';

    public function __construct(
        protected ContentFilterService $filterService,
    ) {}

    /**
     * Fetch recent tweets matching Laravel + AI/skills keywords.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function fetch(): Collection
    {
        $bearerToken = config('services.x.bearer_token');

        if (! $bearerToken) {
            Log::warning('X API bearer token not configured');

            return collect();
        }

        $query = '(laravel OR php) (ai OR skills OR agent OR "claude code") -is:retweet lang:en';

        $response = Http::withToken($bearerToken)
            ->retry(3, 100, fn ($exception) => $exception->getCode() === 429)
            ->get("{$this->baseUrl}/tweets/search/recent", [
                'query' => $query,
                'max_results' => 50,
                'tweet.fields' => 'created_at,public_metrics,author_id',
                'user.fields' => 'name,username,profile_image_url',
                'expansions' => 'author_id,attachments.media_keys',
                'media.fields' => 'url,preview_image_url',
            ]);

        if ($response->failed()) {
            Log::error('X API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $response->throw();
        }

        $data = $response->json();
        $tweets = $data['data'] ?? [];
        $users = collect($data['includes']['users'] ?? [])->keyBy('id');
        $media = collect($data['includes']['media'] ?? [])->keyBy('media_key');

        return collect($tweets)->map(function (array $tweet) use ($users, $media): array {
            $author = $users->get($tweet['author_id'], []);
            $mediaKey = $tweet['attachments']['media_keys'][0] ?? null;
            $mediaItem = $mediaKey ? $media->get($mediaKey) : null;

            return [
                'tweet' => $tweet,
                'author' => $author,
                'media' => $mediaItem,
                'content' => $tweet['text'] ?? '',
            ];
        });
    }

    /**
     * Normalize an X API tweet into the social_posts schema.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalize(array $data): array
    {
        $tweet = $data['tweet'];
        $author = $data['author'];
        $media = $data['media'] ?? null;
        $metrics = $tweet['public_metrics'] ?? [];

        $engagement = ($metrics['like_count'] ?? 0)
            + (($metrics['retweet_count'] ?? 0) * 2)
            + (($metrics['reply_count'] ?? 0) * 3);

        return [
            'platform' => Platform::X,
            'platform_id' => (string) $tweet['id'],
            'author_name' => $author['name'] ?? 'Unknown',
            'author_handle' => $author['username'] ?? 'unknown',
            'author_avatar_url' => $author['profile_image_url'] ?? null,
            'content' => $tweet['text'] ?? '',
            'media_url' => $media['url'] ?? $media['preview_image_url'] ?? null,
            'post_url' => "https://x.com/{$author['username']}/status/{$tweet['id']}",
            'engagement_score' => $engagement,
            'published_at' => Carbon::parse($tweet['created_at']),
            'fetched_at' => now(),
        ];
    }

    /**
     * Check if a raw tweet passes the content filter.
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
        return Platform::X;
    }
}
