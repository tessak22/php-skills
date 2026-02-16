<?php

declare(strict_types=1);

use App\Models\Author;
use App\Models\Category;
use App\Models\Skill;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns paginated skills', function () {
    Skill::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/skills');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'install_command',
                    'content',
                    'is_official',
                    'is_featured',
                    'install_count',
                    'compatible_agents',
                    'tags',
                    'author',
                    'category',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(3, 'data');
});

it('filters skills by search parameter', function () {
    Skill::factory()->create(['name' => 'Laravel Pest Testing', 'slug' => 'laravel-pest-testing']);
    Skill::factory()->create(['name' => 'Eloquent Query Optimization', 'slug' => 'eloquent-query-optimization']);

    $response = $this->getJson('/api/v1/skills?search=Pest');

    $response->assertOk();

    $names = collect($response->json('data'))->pluck('name');
    expect($names)->toContain('Laravel Pest Testing');
});

it('filters skills by category slug', function () {
    $category = Category::factory()->create(['name' => 'Testing & QA', 'slug' => 'testing-qa']);
    $otherCategory = Category::factory()->create(['name' => 'Security', 'slug' => 'security']);

    Skill::factory()->create([
        'name' => 'Laravel Pest Testing',
        'slug' => 'laravel-pest-testing',
        'category_id' => $category->id,
    ]);
    Skill::factory()->create([
        'name' => 'Eloquent Query Optimization',
        'slug' => 'eloquent-query-optimization',
        'category_id' => $otherCategory->id,
    ]);

    $response = $this->getJson('/api/v1/skills?category=testing-qa');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Laravel Pest Testing');
});

it('filters skills by compatible agent', function () {
    Skill::factory()->create([
        'name' => 'Laravel Pest Testing',
        'slug' => 'laravel-pest-testing',
        'compatible_agents' => ['claude-code', 'cursor'],
    ]);
    Skill::factory()->create([
        'name' => 'Eloquent Query Optimization',
        'slug' => 'eloquent-query-optimization',
        'compatible_agents' => ['cursor'],
    ]);

    $response = $this->getJson('/api/v1/skills?agent=claude-code');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Laravel Pest Testing');
});

it('sorts skills by install count', function () {
    Skill::factory()->create([
        'name' => 'Laravel Pest Testing',
        'slug' => 'laravel-pest-testing',
        'install_count' => 100,
    ]);
    Skill::factory()->create([
        'name' => 'Eloquent Query Optimization',
        'slug' => 'eloquent-query-optimization',
        'install_count' => 500,
    ]);

    $response = $this->getJson('/api/v1/skills?sort=installs');

    $response->assertOk();

    $data = $response->json('data');
    expect($data[0]['install_count'])->toBeGreaterThanOrEqual($data[1]['install_count']);
});

it('returns a single skill with author and category', function () {
    $skill = Skill::factory()->create([
        'name' => 'Laravel Pest Testing',
        'slug' => 'laravel-pest-testing',
    ]);

    $response = $this->getJson("/api/v1/skills/{$skill->slug}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'install_command',
                'content',
                'author' => ['id', 'name', 'slug'],
                'category' => ['id', 'name', 'slug'],
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJsonPath('data.slug', 'laravel-pest-testing');
});

it('returns 404 for non-existent skill slug', function () {
    $response = $this->getJson('/api/v1/skills/non-existent-skill');

    $response->assertNotFound();
});

it('requires authentication to create a skill', function () {
    $response = $this->postJson('/api/v1/skills', [
        'name' => 'New Skill',
        'description' => 'A new skill description.',
        'install_command' => 'claude install-skill new-skill',
        'content' => '# New Skill',
    ]);

    $response->assertUnauthorized();
});

it('creates a skill when authenticated', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/skills', [
        'name' => 'Brand New Skill',
        'description' => 'A brand new skill for testing.',
        'install_command' => 'claude install-skill brand-new-skill',
        'content' => '# Brand New Skill\n\nThis is the skill content.',
        'category_id' => $category->id,
        'compatible_agents' => ['claude-code'],
        'tags' => ['laravel', 'testing'],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Brand New Skill')
        ->assertJsonPath('data.slug', 'brand-new-skill');

    $this->assertDatabaseHas('skills', [
        'name' => 'Brand New Skill',
        'slug' => 'brand-new-skill',
    ]);

    $this->assertDatabaseHas('authors', [
        'user_id' => $user->id,
    ]);
});

it('updates a skill when owner', function () {
    $user = User::factory()->create();
    $author = Author::factory()->create(['user_id' => $user->id]);
    $skill = Skill::factory()->create([
        'name' => 'Original Skill Name',
        'slug' => 'original-skill-name',
        'author_id' => $author->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/skills/{$skill->slug}", [
        'description' => 'Updated description for this skill.',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.description', 'Updated description for this skill.');
});

it('returns 403 when updating a skill not owned by user', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $author = Author::factory()->create(['user_id' => $owner->id]);
    $skill = Skill::factory()->create([
        'name' => 'Someone Else Skill',
        'slug' => 'someone-else-skill',
        'author_id' => $author->id,
    ]);

    Sanctum::actingAs($otherUser);

    $response = $this->putJson("/api/v1/skills/{$skill->slug}", [
        'description' => 'Trying to update someone else skill.',
    ]);

    $response->assertForbidden();
});

it('deletes a skill when owner', function () {
    $user = User::factory()->create();
    $author = Author::factory()->create(['user_id' => $user->id]);
    $skill = Skill::factory()->create([
        'name' => 'Skill To Delete',
        'slug' => 'skill-to-delete',
        'author_id' => $author->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson("/api/v1/skills/{$skill->slug}");

    $response->assertOk()
        ->assertJsonPath('message', 'Skill deleted successfully.');

    $this->assertDatabaseMissing('skills', [
        'slug' => 'skill-to-delete',
    ]);
});
