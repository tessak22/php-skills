<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Skill;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class ImportSkillsCommand extends Command
{
    protected $signature = 'skills:import
        {--search=* : Additional search terms beyond the defaults (laravel, php)}
        {--dry-run : Preview what would be imported without saving}';

    protected $description = 'Import skills from the skills.sh public API';

    private const API_BASE = 'https://skills.sh/api/search';

    private const DEFAULT_SEARCHES = ['laravel', 'php'];

    private const GITHUB_RAW_BASE = 'https://raw.githubusercontent.com';

    /** @var array<string, string[]> Branch and path combinations to try for SKILL.md */
    private const SKILL_PATHS = [
        'main' => ['skills', '.claude/skills'],
        'master' => ['skills', '.claude/skills'],
    ];

    public function handle(): int
    {
        $searches = $this->buildSearchTerms();
        $isDryRun = (bool) $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN — no records will be created or updated.');
            $this->newLine();
        }

        $this->info('Fetching skills from skills.sh...');

        $skills = $this->fetchAndDeduplicateSkills($searches);

        if ($skills->isEmpty()) {
            $this->warn('No skills found for the given search terms.');

            return self::SUCCESS;
        }

        $this->info("Found {$skills->count()} unique skills across searches: ".implode(', ', $searches));
        $this->newLine();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($skills->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        foreach ($skills as $skillData) {
            $skillId = $skillData['skillId'] ?? $skillData['name'] ?? 'unknown';
            $source = $skillData['source'] ?? '';

            $bar->setMessage("Processing: {$source}/{$skillId}");

            if ($source === '' || $skillId === 'unknown') {
                $skipped++;
                $bar->advance();

                continue;
            }

            $owner = $this->extractOwner($source);
            $installCount = (int) ($skillData['installs'] ?? 0);

            // Fetch and parse the SKILL.md content
            $fetched = $this->fetchSkillContent($source, $skillId);
            $sourceUrl = $fetched['url'];
            $frontmatter = $this->parseFrontmatter($fetched['content']);
            $skillContent = $this->stripFrontmatter($fetched['content']);

            $name = $frontmatter['name'] ?? $skillId;

            $description = $frontmatter['description']
                ?? "A reusable AI agent skill for {$skillId}.";

            if ($isDryRun) {
                $this->newLine();
                $this->line("  [DRY RUN] Would import: <info>{$name}</info> by <comment>{$owner}</comment>");
                $bar->advance();

                continue;
            }

            try {
                $author = $this->findOrCreateAuthor($owner);

                $slug = Str::slug($name);
                $displayName = Str::limit($name, 250, '');

                // Ensure uniqueness by appending author when there's a name/slug conflict
                if (Skill::where('slug', $slug)->where('source_url', '!=', $sourceUrl)->exists()) {
                    $slug = $slug.'-'.Str::slug($owner);
                    $displayName = $displayName.' ('.Str::title($owner).')';
                }

                // Final safety: if slug still conflicts, append a random suffix
                if (Skill::where('slug', $slug)->where('source_url', '!=', $sourceUrl)->exists()) {
                    $slug = $slug.'-'.Str::random(4);
                }

                $skill = Skill::updateOrCreate(
                    ['source_url' => $sourceUrl],
                    [
                        'name' => $displayName,
                        'slug' => $slug,
                        'description' => $description,
                        'install_command' => "npx skills add {$source} --skill {$skillId}",
                        'content' => $skillContent,
                        'author_id' => $author->id,
                        'install_count' => $installCount,
                        'tags' => $this->buildTags($frontmatter),
                        'compatible_agents' => $frontmatter['metadata']['compatible_agents'] ?? [],
                        'is_official' => false,
                    ],
                );

                if ($skill->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Throwable $e) {
                $skipped++;
                $this->newLine();
                $this->warn("  Skipped {$skillId}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->setMessage('Done!');
        $bar->finish();

        $this->newLine(2);
        $this->printSummary($skills->count(), $created, $updated, $skipped, $isDryRun);

        return self::SUCCESS;
    }

    /**
     * Build the list of search terms from defaults + user-supplied options.
     *
     * @return string[]
     */
    private function buildSearchTerms(): array
    {
        $extra = $this->option('search');
        $terms = array_merge(self::DEFAULT_SEARCHES, is_array($extra) ? $extra : []);

        return array_values(array_unique(array_filter($terms)));
    }

    /**
     * Query skills.sh for each search term and deduplicate by skill ID.
     *
     * @param  string[]  $searches
     * @return Collection<int, array<string, mixed>>
     */
    private function fetchAndDeduplicateSkills(array $searches): Collection
    {
        $all = collect();

        foreach ($searches as $term) {
            $this->line("  Searching for: <comment>{$term}</comment>");

            $response = Http::timeout(30)
                ->retry(3, 500)
                ->get(self::API_BASE, [
                    'q' => $term,
                    'limit' => 100,
                ]);

            if ($response->failed()) {
                $this->warn("  Failed to fetch results for '{$term}' (HTTP {$response->status()})");

                continue;
            }

            $skills = $response->json('skills', []);
            $this->line('  Found <info>'.count($skills)."</info> results for '{$term}'");

            foreach ($skills as $skill) {
                $id = $skill['id'] ?? null;
                if ($id !== null && ! $all->has($id)) {
                    $all->put($id, $skill);
                }
            }
        }

        return $all->values();
    }

    /**
     * Try multiple GitHub raw paths to fetch the SKILL.md content.
     *
     * @return array{content: string, url: string}
     */
    private function fetchSkillContent(string $source, string $skillId): array
    {
        foreach (self::SKILL_PATHS as $branch => $prefixes) {
            foreach ($prefixes as $prefix) {
                $url = self::GITHUB_RAW_BASE."/{$source}/{$branch}/{$prefix}/{$skillId}/SKILL.md";

                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    $browseUrl = "https://github.com/{$source}/blob/{$branch}/{$prefix}/{$skillId}/SKILL.md";

                    return ['content' => $response->body(), 'url' => $browseUrl];
                }
            }
        }

        return ['content' => '', 'url' => "https://github.com/{$source}"];
    }

    /**
     * Parse YAML frontmatter from a SKILL.md string.
     *
     * @return array<string, mixed>
     */
    private function parseFrontmatter(string $markdown): array
    {
        if ($markdown === '' || ! str_starts_with(trim($markdown), '---')) {
            return [];
        }

        $trimmed = ltrim($markdown);

        // Find the closing --- (skip the opening one)
        $endPos = strpos($trimmed, '---', 3);

        if ($endPos === false) {
            return [];
        }

        $yamlBlock = substr($trimmed, 3, $endPos - 3);

        try {
            $parsed = Yaml::parse(trim($yamlBlock));

            return is_array($parsed) ? $parsed : [];
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Remove YAML frontmatter from a SKILL.md string, returning just the body.
     */
    private function stripFrontmatter(string $markdown): string
    {
        if ($markdown === '' || ! str_starts_with(trim($markdown), '---')) {
            return $markdown;
        }

        $trimmed = ltrim($markdown);
        $endPos = strpos($trimmed, '---', 3);

        if ($endPos === false) {
            return $markdown;
        }

        return ltrim(substr($trimmed, $endPos + 3));
    }

    /**
     * Extract the GitHub owner/username from a "owner/repo" source string.
     */
    private function extractOwner(string $source): string
    {
        $parts = explode('/', $source, 2);

        return $parts[0] ?? 'unknown';
    }

    /**
     * Find or create an Author from a GitHub username.
     */
    private function findOrCreateAuthor(string $githubUsername): Author
    {
        return Author::firstOrCreate(
            ['github_username' => $githubUsername],
            [
                'name' => $githubUsername,
                'slug' => Str::slug($githubUsername),
                'avatar_url' => "https://avatars.githubusercontent.com/{$githubUsername}",
            ],
        );
    }

    /**
     * Build the tags array, always including the "skills.sh" source tag.
     *
     * @param  array<string, mixed>  $frontmatter
     * @return string[]
     */
    private function buildTags(array $frontmatter): array
    {
        $tags = ['skills.sh'];

        $fmTags = $frontmatter['metadata']['tags'] ?? $frontmatter['tags'] ?? [];

        if (is_array($fmTags)) {
            $tags = array_merge($tags, $fmTags);
        }

        return array_values(array_unique($tags));
    }

    /**
     * Print a summary table after the import completes.
     */
    private function printSummary(int $total, int $created, int $updated, int $skipped, bool $isDryRun): void
    {
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total skills found', (string) $total],
                [$isDryRun ? 'Would create' : 'Created', (string) $created],
                [$isDryRun ? 'Would update' : 'Updated', (string) $updated],
                ['Skipped (invalid)', (string) $skipped],
            ],
        );

        if ($isDryRun) {
            $this->newLine();
            $this->warn('This was a dry run. Run without --dry-run to persist changes.');
        }
    }
}
