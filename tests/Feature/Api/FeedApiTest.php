<?php

declare(strict_types=1);

use App\Enums\Platform;
use App\Models\SocialPost;

it('returns paginated social posts', function () {
    SocialPost::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/feed');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'platform',
                    'platform_label',
                    'platform_id',
                    'author_name',
                    'author_handle',
                    'content',
                    'post_url',
                    'engagement_score',
                    'is_featured',
                    'is_hidden',
                    'published_at',
                ],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(3, 'data');
});

it('filters social posts by platform', function () {
    SocialPost::factory()->forPlatform(Platform::X)->count(2)->create();
    SocialPost::factory()->forPlatform(Platform::Bluesky)->count(3)->create();

    $response = $this->getJson('/api/v1/feed?platform=x');

    $response->assertOk()
        ->assertJsonCount(2, 'data');

    $platforms = collect($response->json('data'))->pluck('platform')->unique();
    expect($platforms->toArray())->toBe(['x']);
});

it('does not return hidden posts', function () {
    SocialPost::factory()->count(2)->create();
    SocialPost::factory()->hidden()->count(3)->create();

    $response = $this->getJson('/api/v1/feed');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});
