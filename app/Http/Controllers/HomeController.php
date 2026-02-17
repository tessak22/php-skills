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
        $perPage = (int) $request->input('per_page', 12);
        $sort = $request->input('sort', 'installs');
        $search = $request->input('search');
        $categorySlug = $request->input('category');
        $agent = $request->input('agent');

        $query = Skill::query()->with(['author', 'category']);

        if ($search) {
            $query = Skill::search($search)->query(fn ($q) => $q->with(['author', 'category']));
        }

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($agent) {
            $query->whereJsonContains('compatible_agents', $agent);
        }

        if (! $search) {
            $query->ordered($sort);
        }

        $skills = $query->paginate($perPage)->withQueryString();

        $categories = Cache::remember('categories:all', now()->addHours(24), function () {
            return Category::ordered()->get();
        });

        return Inertia::render('Home', [
            'skills' => $skills,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category' => $categorySlug,
                'agent' => $agent,
                'sort' => $sort,
            ],
            'recentPosts' => Inertia::defer(fn () => SocialPost::visible()
                ->ordered()
                ->limit(8)
                ->get()),
        ]);
    }
}
