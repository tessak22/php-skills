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

class YouTubeAdapter implements SocialPlatformInterface
{
    protected string $baseUrl = 'https://www.googleapis.com/youtube/v3';

    public function __construct(
        protected ContentFilterService $filterService,
    ) {}

    /**
     * Fetch videos from YouTube Data API v3 search.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function fetch(): Collection
    {
        $apiKey = config('services.youtube.api_key');

        if (! $apiKey) {
            Log::warning('YouTube API key not configured');

            return collect();
        }

        try {
            $response = Http::retry(3, 100)
                ->get("{$this->baseUrl}/search", [
                    'key' => $apiKey,
                    'q' => 'laravel ai skills OR laravel claude OR php ai agent',
                    'part' => 'snippet',
                    'type' => 'video',
                    'order' => 'date',
                    'maxResults' => 25,
                    'publishedAfter' => now()->subDays(7)->toIso8601String(),
                ]);

            if ($response->failed()) {
                Log::warning('YouTube API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return collect();
            }
        } catch (\Throwable $e) {
            Log::warning('YouTube API unreachable', [
                'error' => $e->getMessage(),
            ]);

            return collect();
        }

        $items = $response->json('items', []);

        if (empty($items)) {
            return collect();
        }

        // Fetch video statistics for engagement scores
        $videoIds = collect($items)->pluck('id.videoId')->filter()->implode(',');

        try {
            $statsResponse = Http::retry(3, 100)
                ->get("{$this->baseUrl}/videos", [
                    'key' => $apiKey,
                    'id' => $videoIds,
                    'part' => 'statistics',
                ]);

            $stats = collect($statsResponse->json('items', []))->keyBy('id');
        } catch (\Throwable $e) {
            Log::warning('YouTube statistics API unreachable, proceeding without stats', [
                'error' => $e->getMessage(),
            ]);

            $stats = collect();
        }

        return collect($items)->map(function (array $item) use ($stats): array {
            $videoId = $item['id']['videoId'] ?? '';
            $videoStats = $stats->get($videoId, []);

            return [
                'item' => $item,
                'stats' => $videoStats['statistics'] ?? [],
                'content' => ($item['snippet']['title'] ?? '').' '.($item['snippet']['description'] ?? ''),
            ];
        });
    }

    /**
     * Normalize a YouTube video into the social_posts schema.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalize(array $data): array
    {
        $item = $data['item'];
        $snippet = $item['snippet'] ?? [];
        $stats = $data['stats'] ?? [];
        $videoId = $item['id']['videoId'] ?? '';

        $thumbnails = $snippet['thumbnails'] ?? [];
        $thumbnail = $thumbnails['high']['url']
            ?? $thumbnails['medium']['url']
            ?? $thumbnails['default']['url']
            ?? null;

        // Engagement: likes x 1 + reposts x 2 + comments x 3
        // YouTube has no repost equivalent, so reposts = 0
        $likeCount = (int) ($stats['likeCount'] ?? 0);
        $commentCount = (int) ($stats['commentCount'] ?? 0);
        $engagement = ($likeCount * 1) + (0 * 2) + ($commentCount * 3);

        return [
            'platform' => Platform::YouTube,
            'platform_id' => $videoId,
            'author_name' => $snippet['channelTitle'] ?? 'Unknown',
            'author_handle' => $snippet['channelId'] ?? '',
            'author_avatar_url' => null,
            'content' => $snippet['title'] ?? '',
            'media_url' => $thumbnail,
            'post_url' => "https://www.youtube.com/watch?v={$videoId}",
            'engagement_score' => $engagement,
            'published_at' => Carbon::parse($snippet['publishedAt'] ?? now()),
            'fetched_at' => now(),
        ];
    }

    /**
     * Check if a YouTube video passes the content filter.
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
        return Platform::YouTube;
    }
}
