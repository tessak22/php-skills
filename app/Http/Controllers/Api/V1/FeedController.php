<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\Platform;
use App\Http\Controllers\Controller;
use App\Http\Resources\SocialPostResource;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeedController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 15);
        $platform = $request->input('platform');

        $query = SocialPost::visible()->ordered();

        if ($platform) {
            $platformEnum = Platform::tryFrom($platform);
            if ($platformEnum) {
                $query->forPlatform($platformEnum);
            }
        }

        $posts = $query->paginate($perPage);

        return SocialPostResource::collection($posts);
    }
}
