<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\FeedController;
use App\Http\Controllers\Api\V1\SkillController;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public
    Route::get('/skills', [SkillController::class, 'index']);
    Route::get('/skills/{skill:slug}', [SkillController::class, 'show']);
    Route::get('/categories', function () {
        $categories = Cache::remember('categories:all', now()->addHours(24), function () {
            return Category::ordered()->get();
        });

        return CategoryResource::collection($categories);
    });
    Route::get('/feed', [FeedController::class, 'index']);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/skills', [SkillController::class, 'store']);
        Route::put('/skills/{skill:slug}', [SkillController::class, 'update']);
        Route::delete('/skills/{skill:slug}', [SkillController::class, 'destroy']);
    });

    // Admin
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::put('/skills/{skill:slug}/feature', [AdminController::class, 'featureSkill']);
        Route::put('/skills/{skill:slug}/verify', [AdminController::class, 'verifySkill']);
        Route::put('/feed/{socialPost}/feature', [AdminController::class, 'featurePost']);
        Route::put('/feed/{socialPost}/hide', [AdminController::class, 'hidePost']);
    });
});
