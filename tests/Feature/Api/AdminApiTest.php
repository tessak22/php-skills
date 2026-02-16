<?php

declare(strict_types=1);

use App\Models\Skill;
use App\Models\SocialPost;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

// --- Feature Skill ---

it('toggles skill featured status as admin', function () {
    $admin = User::factory()->admin()->create();
    $skill = Skill::factory()->create([
        'name' => 'Laravel Pest Testing',
        'slug' => 'laravel-pest-testing',
        'is_featured' => false,
    ]);

    Sanctum::actingAs($admin);

    $response = $this->putJson("/api/v1/admin/skills/{$skill->slug}/feature");

    $response->assertOk()
        ->assertJsonPath('data.is_featured', true);

    // Toggle back
    $response = $this->putJson("/api/v1/admin/skills/{$skill->slug}/feature");

    $response->assertOk()
        ->assertJsonPath('data.is_featured', false);
});

it('returns 403 when non-admin tries to feature a skill', function () {
    $user = User::factory()->create();
    $skill = Skill::factory()->create([
        'name' => 'Laravel Pest Testing',
        'slug' => 'laravel-pest-testing',
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/admin/skills/{$skill->slug}/feature");

    $response->assertForbidden();
});

it('returns 401 when unauthenticated user tries to feature a skill', function () {
    $skill = Skill::factory()->create([
        'name' => 'Laravel Pest Testing',
        'slug' => 'laravel-pest-testing',
    ]);

    $response = $this->putJson("/api/v1/admin/skills/{$skill->slug}/feature");

    $response->assertUnauthorized();
});

// --- Verify Skill ---

it('toggles skill official status as admin', function () {
    $admin = User::factory()->admin()->create();
    $skill = Skill::factory()->create([
        'name' => 'Eloquent Query Optimization',
        'slug' => 'eloquent-query-optimization',
        'is_official' => false,
    ]);

    Sanctum::actingAs($admin);

    $response = $this->putJson("/api/v1/admin/skills/{$skill->slug}/verify");

    $response->assertOk()
        ->assertJsonPath('data.is_official', true);

    // Toggle back
    $response = $this->putJson("/api/v1/admin/skills/{$skill->slug}/verify");

    $response->assertOk()
        ->assertJsonPath('data.is_official', false);
});

it('returns 403 when non-admin tries to verify a skill', function () {
    $user = User::factory()->create();
    $skill = Skill::factory()->create([
        'name' => 'Eloquent Query Optimization',
        'slug' => 'eloquent-query-optimization',
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/admin/skills/{$skill->slug}/verify");

    $response->assertForbidden();
});

it('returns 401 when unauthenticated user tries to verify a skill', function () {
    $skill = Skill::factory()->create([
        'name' => 'Eloquent Query Optimization',
        'slug' => 'eloquent-query-optimization',
    ]);

    $response = $this->putJson("/api/v1/admin/skills/{$skill->slug}/verify");

    $response->assertUnauthorized();
});

// --- Feature Post ---

it('toggles social post featured status as admin', function () {
    $admin = User::factory()->admin()->create();
    $post = SocialPost::factory()->create(['is_featured' => false]);

    Sanctum::actingAs($admin);

    $response = $this->putJson("/api/v1/admin/feed/{$post->id}/feature");

    $response->assertOk()
        ->assertJsonPath('data.is_featured', true);

    // Toggle back
    $response = $this->putJson("/api/v1/admin/feed/{$post->id}/feature");

    $response->assertOk()
        ->assertJsonPath('data.is_featured', false);
});

it('returns 403 when non-admin tries to feature a post', function () {
    $user = User::factory()->create();
    $post = SocialPost::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/admin/feed/{$post->id}/feature");

    $response->assertForbidden();
});

it('returns 401 when unauthenticated user tries to feature a post', function () {
    $post = SocialPost::factory()->create();

    $response = $this->putJson("/api/v1/admin/feed/{$post->id}/feature");

    $response->assertUnauthorized();
});

// --- Hide Post ---

it('toggles social post hidden status as admin', function () {
    $admin = User::factory()->admin()->create();
    $post = SocialPost::factory()->create(['is_hidden' => false]);

    Sanctum::actingAs($admin);

    $response = $this->putJson("/api/v1/admin/feed/{$post->id}/hide");

    $response->assertOk()
        ->assertJsonPath('data.is_hidden', true);

    // Toggle back
    $response = $this->putJson("/api/v1/admin/feed/{$post->id}/hide");

    $response->assertOk()
        ->assertJsonPath('data.is_hidden', false);
});

it('returns 403 when non-admin tries to hide a post', function () {
    $user = User::factory()->create();
    $post = SocialPost::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/admin/feed/{$post->id}/hide");

    $response->assertForbidden();
});

it('returns 401 when unauthenticated user tries to hide a post', function () {
    $post = SocialPost::factory()->create();

    $response = $this->putJson("/api/v1/admin/feed/{$post->id}/hide");

    $response->assertUnauthorized();
});
