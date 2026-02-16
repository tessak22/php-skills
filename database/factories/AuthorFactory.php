<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'github_username' => fake()->userName(),
            'avatar_url' => 'https://i.pravatar.cc/150?u='.fake()->userName(),
            'bio' => fake()->sentence(15),
            'user_id' => null,
        ];
    }
}
