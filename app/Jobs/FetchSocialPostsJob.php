<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\SocialPlatformInterface;
use App\Models\SocialPost;
use App\Services\ContentFilterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchSocialPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $adapterClass,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ContentFilterService $filterService): void
    {
        $adapter = app($this->adapterClass);

        if (! $adapter instanceof SocialPlatformInterface) {
            Log::error('Invalid social platform adapter', [
                'adapter' => $this->adapterClass,
            ]);

            return;
        }

        $platform = $adapter->platform();
        $cacheKey = "social:raw_posts:{$platform->value}";

        Log::info('Fetching social posts', [
            'platform' => $platform->value,
            'adapter' => $this->adapterClass,
        ]);

        try {
            $posts = $adapter->fetch();

            // Cache fresh data for stale-serving when APIs are down (1 hour TTL)
            if ($posts->isNotEmpty()) {
                Cache::put($cacheKey, $posts, now()->addHour());
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch social posts, serving stale data', [
                'platform' => $platform->value,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

            // Stale > empty: serve cached data when API is down
            $posts = Cache::get($cacheKey, collect());

            if ($posts->isEmpty()) {
                Log::warning('No stale data available for platform, skipping sync', [
                    'platform' => $platform->value,
                ]);

                return;
            }

            Log::info('Using stale cached data for platform', [
                'platform' => $platform->value,
                'stale_count' => $posts->count(),
            ]);
        }

        $this->processPosts($adapter, $filterService, $platform, $posts);
    }

    /**
     * Process and persist fetched social posts.
     */
    protected function processPosts(
        SocialPlatformInterface $adapter,
        ContentFilterService $filterService,
        \App\Enums\Platform $platform,
        Collection $posts,
    ): void {
        $created = 0;
        $updated = 0;
        $filtered = 0;

        foreach ($posts as $rawPost) {
            if (! $adapter->filter($rawPost) && ! $filterService->passes($rawPost['content'] ?? '')) {
                $filtered++;

                continue;
            }

            $normalized = $adapter->normalize($rawPost);

            $post = SocialPost::updateOrCreate(
                [
                    'platform' => $normalized['platform'],
                    'platform_id' => $normalized['platform_id'],
                ],
                $normalized,
            );

            if ($post->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        Log::info('Social posts sync complete', [
            'platform' => $platform->value,
            'fetched' => $posts->count(),
            'created' => $created,
            'updated' => $updated,
            'filtered' => $filtered,
        ]);
    }

    /**
     * Handle a job failure — log which platform adapter failed.
     */
    public function failed(?\Throwable $exception): void
    {
        // Resolve the platform name for clearer logging
        $platformName = 'unknown';

        try {
            $adapter = app($this->adapterClass);
            if ($adapter instanceof SocialPlatformInterface) {
                $platformName = $adapter->platform()->value;
            }
        } catch (\Throwable) {
            // If we can't resolve the adapter, use the class name
            $platformName = class_basename($this->adapterClass);
        }

        Log::error('FetchSocialPostsJob failed permanently', [
            'adapter' => $this->adapterClass,
            'platform' => $platformName,
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);
    }
}
