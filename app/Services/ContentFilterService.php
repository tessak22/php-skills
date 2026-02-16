<?php

declare(strict_types=1);

namespace App\Services;

class ContentFilterService
{
    /**
     * Group A: Framework keywords (at least one required).
     *
     * @var list<string>
     */
    protected const array GROUP_A = [
        'laravel',
        'php',
        'eloquent',
        'artisan',
        'blade',
        'livewire',
        'inertia',
        'filament',
        'nova',
        'vapor',
        'forge',
        'laravel cloud',
        'pest',
        'sail',
    ];

    /**
     * Group B: AI/Skills keywords (at least one required).
     *
     * @var list<string>
     */
    protected const array GROUP_B = [
        'ai',
        'skill',
        'skills',
        'agent',
        'claude',
        'cursor',
        'copilot',
        'windsurf',
        'chatgpt',
        'llm',
        'prompt',
        'vibe coding',
        'ai-first',
        'coding agent',
        'claude code',
    ];

    /**
     * Branded hashtag that automatically qualifies a post.
     */
    protected const string BRANDED_HASHTAG = '#laravelskills';

    /**
     * Determine if content passes the keyword filter.
     *
     * A post passes if:
     * - It contains at least one keyword from Group A AND at least one from Group B, OR
     * - It contains the branded hashtag.
     */
    public function passes(string $content): bool
    {
        $lower = mb_strtolower($content);

        if (str_contains($lower, self::BRANDED_HASHTAG)) {
            return true;
        }

        return $this->containsAny($lower, self::GROUP_A)
            && $this->containsAny($lower, self::GROUP_B);
    }

    /**
     * Check if text contains any of the given keywords.
     *
     * @param  list<string>  $keywords
     */
    protected function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
