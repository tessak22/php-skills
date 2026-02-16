<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Skill;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportSkillsCommand extends Command
{
    protected $signature = 'skills:import';

    protected $description = 'Import pre-populated PHP/Laravel skills from a hardcoded dataset';

    public function handle(): int
    {
        $skills = $this->getSkillsData();

        $count = count($skills);
        $this->info("Importing {$count} skills...");
        $this->newLine();

        $imported = 0;
        $updated = 0;

        foreach ($skills as $entry) {
            $author = Author::firstOrCreate(
                ['slug' => Str::slug($entry['author']['name'])],
                [
                    'name' => $entry['author']['name'],
                    'github_username' => $entry['author']['github_username'] ?? null,
                    'avatar_url' => $entry['author']['avatar_url'] ?? null,
                    'bio' => $entry['author']['bio'] ?? null,
                ]
            );

            $skill = Skill::updateOrCreate(
                ['source_url' => $entry['source_url']],
                [
                    'name' => $entry['name'],
                    'slug' => Str::slug($entry['name']),
                    'description' => $entry['description'],
                    'install_command' => $entry['install_command'],
                    'content' => $entry['content'],
                    'author_id' => $author->id,
                    'source_url' => $entry['source_url'],
                    'tags' => $entry['tags'] ?? [],
                    'compatible_agents' => $entry['compatible_agents'] ?? [],
                    'is_official' => $entry['is_official'] ?? false,
                ]
            );

            if ($skill->wasRecentlyCreated) {
                $imported++;
                $this->line("  + Created: {$entry['name']}");
            } else {
                $updated++;
                $this->line("  ~ Updated: {$entry['name']}");
            }
        }

        $this->newLine();
        $this->info("Done! Created: {$imported}, Updated: {$updated}");

        return self::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getSkillsData(): array
    {
        return [
            [
                'name' => 'Laravel CRUD Generator',
                'description' => 'Generate complete CRUD operations for Laravel models including migration, model, controller, form requests, and API resources.',
                'install_command' => 'claude skills add laravel-crud-generator',
                'content' => "# Laravel CRUD Generator\n\nGenerate complete CRUD scaffolding for any Laravel model.\n\n## What it does\n- Creates migration with proper column types\n- Generates Eloquent model with fillable, casts, and relationships\n- Builds API controller with index, show, store, update, destroy\n- Creates Form Request classes with validation rules\n- Generates API Resource for JSON serialization\n\n## Usage\nDescribe the model you want (e.g. \"a BlogPost with title, body, author, published_at\") and this skill handles the rest.",
                'author' => [
                    'name' => 'Taylor Otwell',
                    'github_username' => 'taylorotwell',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/463230',
                    'bio' => 'Creator of Laravel',
                ],
                'source_url' => 'https://skills.sh/skills/laravel-crud-generator',
                'tags' => ['laravel', 'crud', 'scaffolding', 'eloquent'],
                'compatible_agents' => ['Claude Code', 'Cursor', 'Windsurf'],
                'is_official' => true,
            ],
            [
                'name' => 'Laravel Test Writer',
                'description' => 'Write comprehensive Pest PHP tests for Laravel applications including feature tests, unit tests, and database assertions.',
                'install_command' => 'claude skills add laravel-test-writer',
                'content' => "# Laravel Test Writer\n\nAutomatically generate Pest PHP tests for your Laravel code.\n\n## What it does\n- Analyzes existing controllers and models\n- Generates feature tests with proper HTTP assertions\n- Creates unit tests for business logic\n- Uses factories and database transactions\n- Covers happy paths and edge cases\n\n## Conventions\n- Uses Pest PHP syntax (not PHPUnit)\n- RefreshDatabase trait on all feature tests\n- Factories for test data\n- Descriptive test names using `it()` syntax",
                'author' => [
                    'name' => 'Nuno Maduro',
                    'github_username' => 'nunomaduro',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/5457236',
                    'bio' => 'Creator of Pest PHP, Laravel core team',
                ],
                'source_url' => 'https://skills.sh/skills/laravel-test-writer',
                'tags' => ['laravel', 'testing', 'pest', 'phpunit'],
                'compatible_agents' => ['Claude Code', 'Cursor', 'Windsurf'],
                'is_official' => true,
            ],
            [
                'name' => 'Laravel Migration Expert',
                'description' => 'Create well-structured Laravel database migrations with proper indexes, foreign keys, and column types.',
                'install_command' => 'claude skills add laravel-migration-expert',
                'content' => "# Laravel Migration Expert\n\nCreate production-ready database migrations for Laravel.\n\n## What it does\n- Generates migrations with correct column types\n- Adds proper indexes for query performance\n- Sets up foreign key constraints\n- Handles nullable/default values correctly\n- Creates pivot tables for many-to-many relationships\n\n## Best practices applied\n- ULIDs over auto-incrementing IDs\n- Soft deletes when appropriate\n- Timestamp columns with timezone awareness\n- Composite indexes for common query patterns",
                'author' => [
                    'name' => 'Mohamed Said',
                    'github_username' => 'themsaid',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/4332404',
                    'bio' => 'Laravel core team member',
                ],
                'source_url' => 'https://skills.sh/skills/laravel-migration-expert',
                'tags' => ['laravel', 'database', 'migrations', 'postgresql'],
                'compatible_agents' => ['Claude Code', 'Cursor'],
                'is_official' => false,
            ],
            [
                'name' => 'Inertia Vue Page Builder',
                'description' => 'Build Inertia.js pages with Vue 3 composition API, proper TypeScript types, and Tailwind CSS styling.',
                'install_command' => 'claude skills add inertia-vue-page-builder',
                'content' => "# Inertia Vue Page Builder\n\nBuild full-stack Laravel + Inertia.js + Vue 3 pages.\n\n## What it does\n- Creates Vue SFCs with <script setup> syntax\n- Generates proper Inertia page props\n- Builds forms with useForm() helper\n- Implements pagination with Inertia links\n- Adds Tailwind CSS responsive styling\n\n## Stack\n- Vue 3 Composition API\n- Inertia.js v2\n- Tailwind CSS 4\n- TypeScript optional",
                'author' => [
                    'name' => 'Jonathan Reinink',
                    'github_username' => 'reinink',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/882133',
                    'bio' => 'Creator of Inertia.js',
                ],
                'source_url' => 'https://skills.sh/skills/inertia-vue-page-builder',
                'tags' => ['inertia', 'vue', 'frontend', 'tailwind'],
                'compatible_agents' => ['Claude Code', 'Cursor', 'Windsurf'],
                'is_official' => false,
            ],
            [
                'name' => 'Laravel API Resource Designer',
                'description' => 'Design clean, consistent API resources and resource collections with conditional relationships and computed fields.',
                'install_command' => 'claude skills add laravel-api-resource-designer',
                'content' => "# Laravel API Resource Designer\n\nDesign production-quality API resources for Laravel.\n\n## What it does\n- Creates JsonResource classes with proper typing\n- Uses whenLoaded() for conditional relationships\n- Adds computed/derived fields\n- Builds ResourceCollection classes when needed\n- Implements pagination metadata\n\n## Conventions\n- camelCase JSON keys\n- ISO 8601 date formatting\n- Consistent null handling\n- HATEOAS links when appropriate",
                'author' => [
                    'name' => 'Freek Van der Herten',
                    'github_username' => 'freekmurze',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/483853',
                    'bio' => 'Developer at Spatie, Laravel community leader',
                ],
                'source_url' => 'https://skills.sh/skills/laravel-api-resource-designer',
                'tags' => ['laravel', 'api', 'resources', 'json'],
                'compatible_agents' => ['Claude Code', 'Cursor'],
                'is_official' => false,
            ],
            [
                'name' => 'PHP Enum Builder',
                'description' => 'Create well-structured PHP 8.1+ backed enums with helper methods, labels, and Laravel integration.',
                'install_command' => 'claude skills add php-enum-builder',
                'content' => "# PHP Enum Builder\n\nBuild PHP 8.1+ backed enums with Laravel integrations.\n\n## What it does\n- Creates string or integer backed enums\n- Adds label() and description() methods\n- Implements tryFromLabel() for reverse lookup\n- Generates Eloquent casts\n- Creates validation rules\n\n## Patterns\n- String-backed enums for database storage\n- Label methods for UI display\n- Collection helpers for select dropdowns\n- Implements HasColor for Filament integration",
                'author' => [
                    'name' => 'Luke Downing',
                    'github_username' => 'lukeraymonddowning',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/33256403',
                    'bio' => 'Laravel developer and educator',
                ],
                'source_url' => 'https://skills.sh/skills/php-enum-builder',
                'tags' => ['php', 'enums', 'php8', 'laravel'],
                'compatible_agents' => ['Claude Code', 'Cursor', 'Windsurf'],
                'is_official' => false,
            ],
            [
                'name' => 'Laravel Queue Job Creator',
                'description' => 'Create robust queue jobs with proper error handling, retries, rate limiting, and middleware.',
                'install_command' => 'claude skills add laravel-queue-job-creator',
                'content' => "# Laravel Queue Job Creator\n\nCreate production-ready queued jobs for Laravel.\n\n## What it does\n- Generates job classes with proper interfaces\n- Adds retry logic and backoff strategies\n- Implements rate limiting middleware\n- Handles failure scenarios gracefully\n- Sets up job batching when needed\n\n## Best practices\n- ShouldQueue + ShouldBeUnique when appropriate\n- Configurable retry attempts and backoff\n- Dead letter handling\n- Job chaining for workflows\n- Horizon-friendly tagging",
                'author' => [
                    'name' => 'Jess Archer',
                    'github_username' => 'jessarcher',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/4977161',
                    'bio' => 'Laravel core team member',
                ],
                'source_url' => 'https://skills.sh/skills/laravel-queue-job-creator',
                'tags' => ['laravel', 'queues', 'jobs', 'async'],
                'compatible_agents' => ['Claude Code', 'Cursor'],
                'is_official' => false,
            ],
            [
                'name' => 'Laravel Livewire Component',
                'description' => 'Build reactive Livewire v3 components with proper lifecycle hooks, validation, and Alpine.js integration.',
                'install_command' => 'claude skills add laravel-livewire-component',
                'content' => "# Laravel Livewire Component Builder\n\nBuild Livewire v3 components with best practices.\n\n## What it does\n- Creates Livewire component classes with typed properties\n- Implements form objects for complex forms\n- Adds real-time validation\n- Integrates Alpine.js for client-side interactivity\n- Handles file uploads and pagination\n\n## Conventions\n- Livewire v3 attribute syntax (#[Layout], #[Rule])\n- Form objects for multi-field forms\n- Lazy loading for performance\n- Event dispatching between components",
                'author' => [
                    'name' => 'Caleb Porzio',
                    'github_username' => 'calebporzio',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/5431903',
                    'bio' => 'Creator of Livewire and Alpine.js',
                ],
                'source_url' => 'https://skills.sh/skills/laravel-livewire-component',
                'tags' => ['laravel', 'livewire', 'alpine', 'reactive'],
                'compatible_agents' => ['Claude Code', 'Cursor', 'Windsurf'],
                'is_official' => false,
            ],
            [
                'name' => 'Laravel Authorization Policy',
                'description' => 'Generate Laravel authorization policies with proper gate definitions, role checks, and resource-based permissions.',
                'install_command' => 'claude skills add laravel-authorization-policy',
                'content' => "# Laravel Authorization Policy Builder\n\nGenerate comprehensive authorization policies for Laravel.\n\n## What it does\n- Creates Policy classes for Eloquent models\n- Defines viewAny, view, create, update, delete, restore, forceDelete\n- Implements role-based access control\n- Registers policies in AuthServiceProvider\n- Adds Gate definitions for non-model permissions\n\n## Patterns\n- Resource policies tied to models\n- before() method for super-admin bypass\n- Team/tenant scoping\n- Consistent deny responses with messages",
                'author' => [
                    'name' => 'Tim MacDonald',
                    'github_username' => 'timacdonald',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/24803032',
                    'bio' => 'Laravel core team member',
                ],
                'source_url' => 'https://skills.sh/skills/laravel-authorization-policy',
                'tags' => ['laravel', 'authorization', 'policies', 'gates'],
                'compatible_agents' => ['Claude Code', 'Cursor'],
                'is_official' => false,
            ],
            [
                'name' => 'Laravel Blade Component',
                'description' => 'Build reusable Blade components with proper props, slots, and Tailwind styling for Laravel applications.',
                'install_command' => 'claude skills add laravel-blade-component',
                'content' => "# Laravel Blade Component Builder\n\nBuild reusable, well-structured Blade components.\n\n## What it does\n- Creates class-based Blade components\n- Defines typed constructor props\n- Implements named slots for flexibility\n- Adds Tailwind CSS with responsive design\n- Supports component attributes merging\n\n## Conventions\n- Class-based components for complex logic\n- Anonymous components for simple UI elements\n- @props directive for type safety\n- Consistent naming: x-button, x-card, x-modal\n- Dark mode support via Tailwind",
                'author' => [
                    'name' => 'Dries Vints',
                    'github_username' => 'driesvints',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/594614',
                    'bio' => 'Laravel core team member',
                ],
                'source_url' => 'https://skills.sh/skills/laravel-blade-component',
                'tags' => ['laravel', 'blade', 'components', 'tailwind'],
                'compatible_agents' => ['Claude Code', 'Cursor', 'Windsurf'],
                'is_official' => false,
            ],
        ];
    }
}
