<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Skill;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $featuredSkills = Cache::remember('home:featured_skills', now()->addMinutes(15), function () {
            return Skill::featured()
                ->with(['author', 'category'])
                ->latest()
                ->limit(6)
                ->get();
        });

        $recentPosts = Cache::remember('home:recent_posts', now()->addMinutes(5), function () {
            return SocialPost::visible()
                ->ordered()
                ->limit(8)
                ->get();
        });

        $categories = Cache::remember('categories:all', now()->addHours(24), function () {
            return Category::ordered()->get();
        });

        return Inertia::render('Home', [
            'featuredSkills' => $featuredSkills,
            'recentPosts' => $recentPosts,
            'categories' => $categories,
        ]);
    }
}
