<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Platform;
use App\Http\Resources\SocialPostResource;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedPageController extends Controller
{
    public function index(Request $request): Response
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

        $posts = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Feed/Index', [
            'posts' => SocialPostResource::collection($posts),
            'platforms' => collect(Platform::cases())->map(fn (Platform $p) => [
                'value' => $p->value,
                'label' => $p->label(),
            ]),
            'filters' => [
                'platform' => $platform,
            ],
        ]);
    }
}
