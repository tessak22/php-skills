<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubmitSkillController extends Controller
{
    public function create(Request $request): Response
    {
        $categories = Category::ordered()->get();

        return Inertia::render('Skills/Submit', [
            'categories' => $categories,
        ]);
    }
}
