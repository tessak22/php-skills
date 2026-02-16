<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\Platform;
use App\Http\Controllers\Controller;
use App\Http\Resources\SocialPostResource;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 15);
        $platform = $request->input('platform', 'all');
        $page = (int) $request->input('page', 1);

        $cacheKey = "feed:posts:{$platform}:page:{$page}:per_page:{$perPage}";

        $posts = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($perPage, $platform) {
            $query = SocialPost::visible()->ordered();

            if ($platform !== 'all') {
                $platformEnum = Platform::tryFrom($platform);
                if ($platformEnum) {
                    $query->forPlatform($platformEnum);
                }
            }

            return $query->paginate($perPage);
        });

        return SocialPostResource::collection($posts);
    }
}
