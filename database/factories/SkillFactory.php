<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Laravel Pest Testing',
            'Eloquent Query Optimization',
            'Laravel API Authentication',
            'Inertia.js Best Practices',
            'Laravel Queue Management',
            'Blade Component Patterns',
            'Laravel Middleware Guide',
            'Database Migration Strategies',
            'Laravel Event Sourcing',
            'Redis Caching Patterns',
            'Laravel Sanctum Setup',
            'Tailwind CSS with Laravel',
            'Laravel Scout Search',
            'Eloquent Relationships',
            'Laravel Deployment Guide',
            'PHP 8.3 Features in Laravel',
            'Laravel Validation Rules',
            'Service Container Patterns',
            'Laravel Notification System',
            'Artisan Command Development',
            'Laravel Policy & Gates',
            'File Storage in Laravel',
            'Laravel Broadcasting',
            'Rate Limiting Strategies',
            'Laravel Pint Configuration',
        ]);

        $agents = ['claude-code', 'cursor', 'windsurf', 'copilot'];

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(2),
            'install_command' => 'claude install-skill '.Str::slug($name),
            'content' => $this->generateSkillContent($name),
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
            'source_url' => fake()->optional(0.7)->url(),
            'is_official' => fake()->boolean(15),
            'is_featured' => fake()->boolean(20),
            'install_count' => fake()->numberBetween(0, 5000),
            'compatible_agents' => fake()->randomElements($agents, fake()->numberBetween(1, 4)),
            'tags' => fake()->randomElements(['laravel', 'php', 'testing', 'api', 'eloquent', 'auth', 'performance', 'devops'], fake()->numberBetween(1, 4)),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function official(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_official' => true,
        ]);
    }

    private function generateSkillContent(string $name): string
    {
        return <<<MARKDOWN
        # {$name}

        ## Overview

        This skill provides guidance for AI coding assistants working with Laravel projects.

        ## Instructions

        When working on Laravel projects, follow these patterns:

        1. Always use `declare(strict_types=1)` in PHP files
        2. Follow PSR-12 coding standards
        3. Use Laravel conventions for naming and structure
        4. Write Pest tests for new functionality

        ## Examples

        ```php
        // Example usage
        \$result = SomeClass::handle(\$input);
        ```

        ## Notes

        - Compatible with Laravel 12+
        - Requires PHP 8.2+
        MARKDOWN;
    }
}
