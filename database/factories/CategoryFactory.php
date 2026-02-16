<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Testing & QA',
            'Authentication',
            'Database & Eloquent',
            'API Development',
            'DevOps & Deployment',
            'Frontend & Inertia',
            'Security',
            'Performance',
            'Code Quality',
            'AI & Automation',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(12),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
