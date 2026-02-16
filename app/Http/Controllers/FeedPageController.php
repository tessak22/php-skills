<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Platform;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            'posts' => $posts,
            'platforms' => Cache::remember('feed:platforms', now()->addHours(24), function () {
                return collect(Platform::cases())->map(fn (Platform $p) => [
                    'value' => $p->value,
                    'label' => $p->label(),
                ])->all();
            }),
            'currentPlatform' => $platform,
        ]);
    }
}
