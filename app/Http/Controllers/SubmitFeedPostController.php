<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Platform;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubmitFeedPostController extends Controller
{
    public function create(Request $request): Response
    {
        $platforms = collect(Platform::cases())->map(fn (Platform $p) => [
            'value' => $p->value,
            'label' => $p->label(),
        ])->all();

        return Inertia::render('Feed/Submit', [
            'platforms' => $platforms,
        ]);
    }
}
