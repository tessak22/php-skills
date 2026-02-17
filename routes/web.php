<?php

declare(strict_types=1);

use App\Http\Controllers\DocsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SkillDirectoryController;
use App\Http\Controllers\SubmitFeedPostController;
use App\Http\Controllers\SubmitSkillController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/skills/{skill:slug}', [SkillDirectoryController::class, 'show'])->name('skills.show');

Route::get('/feed/submit', [SubmitFeedPostController::class, 'create'])->name('feed.submit');

Route::get('/submit', [SubmitSkillController::class, 'create'])
    ->name('skills.submit');

Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');

require __DIR__.'/settings.php';
