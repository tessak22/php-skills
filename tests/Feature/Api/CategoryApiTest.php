<?php

declare(strict_types=1);

use App\Models\Category;

it('returns all categories ordered by sort_order', function () {
    Category::factory()->create(['name' => 'Security', 'slug' => 'security', 'sort_order' => 3]);
    Category::factory()->create(['name' => 'Testing & QA', 'slug' => 'testing-qa', 'sort_order' => 1]);
    Category::factory()->create(['name' => 'API Development', 'slug' => 'api-development', 'sort_order' => 2]);

    $response = $this->getJson('/api/v1/categories');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'sort_order',
                ],
            ],
        ])
        ->assertJsonCount(3, 'data');

    $sortOrders = collect($response->json('data'))->pluck('sort_order')->toArray();
    expect($sortOrders)->toBe([1, 2, 3]);
});
