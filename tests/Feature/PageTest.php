<?php

declare(strict_types=1);

use App\Models\Skill;
use App\Models\User;

it('renders the home page', function () {
    $response = $this->get('/');

    $response->assertOk();
});

it('renders the skills directory page', function () {
    $response = $this->get('/skills');

    $response->assertOk();
});

it('renders a skill detail page for a valid skill', function () {
    $skill = Skill::factory()->create([
        'name' => 'Laravel Pest Testing',
        'slug' => 'laravel-pest-testing',
    ]);

    $response = $this->get("/skills/{$skill->slug}");

    $response->assertOk();
});

it('renders the feed page', function () {
    $response = $this->get('/feed');

    $response->assertOk();
});

it('renders the docs page', function () {
    $response = $this->get('/docs');

    $response->assertOk();
});

it('renders submit page for guests', function () {
    $response = $this->get('/submit');

    $response->assertOk();
});

it('renders the submit page for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/submit');

    $response->assertOk();
});
