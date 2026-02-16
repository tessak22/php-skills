<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\SkillResource;
use App\Http\Resources\SocialPostResource;
use App\Models\Category;
use App\Models\Skill;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $featuredSkills = Skill::featured()
            ->with(['author', 'category'])
            ->latest()
            ->limit(6)
            ->get();

        $recentPosts = SocialPost::visible()
            ->ordered()
            ->limit(8)
            ->get();

        $categories = Category::ordered()->get();

        return Inertia::render('Home', [
            'featuredSkills' => SkillResource::collection($featuredSkills),
            'recentPosts' => SocialPostResource::collection($recentPosts),
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
