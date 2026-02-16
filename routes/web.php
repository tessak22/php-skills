<?php

declare(strict_types=1);

use App\Http\Controllers\DocsController;
use App\Http\Controllers\FeedPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SkillDirectoryController;
use App\Http\Controllers\SubmitSkillController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/skills', [SkillDirectoryController::class, 'index'])->name('skills.index');
Route::get('/skills/{skill:slug}', [SkillDirectoryController::class, 'show'])->name('skills.show');

Route::get('/feed', [FeedPageController::class, 'index'])->name('feed.index');

Route::get('/submit', [SubmitSkillController::class, 'create'])
    ->middleware('auth')
    ->name('skills.submit');

Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
