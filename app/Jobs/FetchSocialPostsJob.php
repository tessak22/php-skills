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

        Log::info('Fetching social posts', [
            'platform' => $platform->value,
            'adapter' => $this->adapterClass,
        ]);

        try {
            $posts = $adapter->fetch();
        } catch (\Throwable $e) {
            Log::error('Failed to fetch social posts', [
                'platform' => $platform->value,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

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
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('FetchSocialPostsJob failed permanently', [
            'adapter' => $this->adapterClass,
            'error' => $exception?->getMessage(),
        ]);
    }
}
