<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Http\Resources\SocialPostResource;
use App\Models\Skill;
use App\Models\SocialPost;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function featureSkill(Skill $skill): JsonResponse
    {
        $skill->update(['is_featured' => ! $skill->is_featured]);

        return response()->json([
            'message' => $skill->is_featured ? 'Skill featured.' : 'Skill unfeatured.',
            'data' => new SkillResource($skill->load(['author', 'category'])),
        ]);
    }

    public function verifySkill(Skill $skill): JsonResponse
    {
        $skill->update(['is_official' => ! $skill->is_official]);

        return response()->json([
            'message' => $skill->is_official ? 'Skill verified.' : 'Skill unverified.',
            'data' => new SkillResource($skill->load(['author', 'category'])),
        ]);
    }

    public function featurePost(SocialPost $socialPost): JsonResponse
    {
        $socialPost->update(['is_featured' => ! $socialPost->is_featured]);

        return response()->json([
            'message' => $socialPost->is_featured ? 'Post featured.' : 'Post unfeatured.',
            'data' => new SocialPostResource($socialPost),
        ]);
    }

    public function hidePost(SocialPost $socialPost): JsonResponse
    {
        $socialPost->update(['is_hidden' => ! $socialPost->is_hidden]);

        return response()->json([
            'message' => $socialPost->is_hidden ? 'Post hidden.' : 'Post unhidden.',
            'data' => new SocialPostResource($socialPost),
        ]);
    }
}
