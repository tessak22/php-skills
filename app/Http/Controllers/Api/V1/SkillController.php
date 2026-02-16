<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Author;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 12);
        $sort = $request->input('sort', 'newest');
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

        $skills = $query->paginate($perPage);

        return SkillResource::collection($skills);
    }

    public function show(Skill $skill): SkillResource
    {
        $skill->load(['author', 'category']);

        return new SkillResource($skill);
    }

    public function store(StoreSkillRequest $request): JsonResponse
    {
        $user = $request->user();

        $author = Author::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'slug' => Str::slug($user->name).'-'.Str::random(4),
            ]
        );

        $skill = Skill::create([
            ...$request->validated(),
            'slug' => Str::slug($request->validated('name')),
            'author_id' => $author->id,
        ]);

        $skill->load(['author', 'category']);

        return (new SkillResource($skill))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateSkillRequest $request, Skill $skill): SkillResource
    {
        $user = $request->user();

        if ($skill->author?->user_id !== $user->id) {
            abort(403, 'You are not authorized to update this skill.');
        }

        $validated = $request->validated();

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $skill->update($validated);
        $skill->load(['author', 'category']);

        return new SkillResource($skill);
    }

    public function destroy(Request $request, Skill $skill): JsonResponse
    {
        $user = $request->user();

        if ($skill->author?->user_id !== $user->id) {
            abort(403, 'You are not authorized to delete this skill.');
        }

        $skill->delete();

        return response()->json(['message' => 'Skill deleted successfully.'], 200);
    }
}
