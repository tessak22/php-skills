<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Code2, Download, Pencil, Terminal, Zap } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type Agent = {
    name: string;
    description: string;
    installPrefix: string;
};

const agents: Agent[] = [
    {
        name: 'Claude Code',
        description: 'Anthropic\'s AI coding assistant for the terminal. Excels at understanding entire codebases, running commands, and writing production-quality code.',
        installPrefix: 'claude skill install',
    },
    {
        name: 'Cursor',
        description: 'An AI-first code editor built on VS Code. Uses AI to help you write, edit, and understand code with natural language.',
        installPrefix: 'cursor skill install',
    },
    {
        name: 'Windsurf',
        description: 'Codeium\'s AI-powered IDE that combines AI assistance with a familiar editing experience. Supports inline completions and chat.',
        installPrefix: 'windsurf skill install',
    },
    {
        name: 'Copilot',
        description: 'GitHub\'s AI pair programmer. Integrates into your editor to suggest code completions and help with common patterns.',
        installPrefix: 'copilot skill install',
    },
];
</script>

<template>
    <Head title="Documentation - LaravelSkills" />

    <div class="px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex items-center gap-3">
                    <BookOpen class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Documentation
                    </h1>
                </div>
                <p class="mt-3 text-lg text-muted-foreground">
                    Everything you need to know about installing, creating, and sharing AI agent skills for Laravel.
                </p>
            </div>

            <!-- How to Install Skills -->
            <section class="mb-12">
                <div class="mb-6 flex items-center gap-2">
                    <Download class="h-6 w-6 text-primary" />
                    <h2 class="text-2xl font-bold text-foreground">How to Install Skills</h2>
                </div>

                <div class="space-y-6">
                    <p class="text-muted-foreground">
                        Installing a skill adds AI-powered procedural knowledge to your coding assistant.
                        Each skill is a curated set of instructions that teaches your AI agent how to work
                        with specific Laravel patterns, packages, and workflows.
                    </p>

                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-base">
                                <Terminal class="h-4 w-4" />
                                Quick Install
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <p class="text-sm text-muted-foreground">
                                Find a skill in the directory and copy the install command. For example:
                            </p>
                            <div class="rounded-lg bg-muted p-4">
                                <code class="text-sm font-mono text-foreground">
                                    claude skill install laravel-eloquent-expert
                                </code>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                The command syntax varies by agent. Each skill page shows the correct command for your agent.
                            </p>
                        </CardContent>
                    </Card>

                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-foreground">Steps</h3>
                        <ol class="list-inside list-decimal space-y-3 text-muted-foreground">
                            <li>
                                <strong class="text-foreground">Browse the directory</strong> --
                                Find a skill that matches your needs at
                                <Link href="/skills" class="text-primary hover:underline">/skills</Link>.
                            </li>
                            <li>
                                <strong class="text-foreground">Copy the install command</strong> --
                                Click the copy button on the skill detail page.
                            </li>
                            <li>
                                <strong class="text-foreground">Run in your terminal</strong> --
                                Paste the command in your project's terminal. The skill will be available immediately.
                            </li>
                            <li>
                                <strong class="text-foreground">Start using it</strong> --
                                Your AI agent now has the skill's knowledge. Ask it about the topic covered.
                            </li>
                        </ol>
                    </div>
                </div>
            </section>

            <Separator class="mb-12" />

            <!-- How to Create a Skill -->
            <section class="mb-12">
                <div class="mb-6 flex items-center gap-2">
                    <Pencil class="h-6 w-6 text-primary" />
                    <h2 class="text-2xl font-bold text-foreground">How to Create a Skill</h2>
                </div>

                <div class="space-y-6">
                    <p class="text-muted-foreground">
                        Skills are Markdown-based documents that encode procedural knowledge for AI agents.
                        A good skill teaches an AI assistant how to approach a specific task or use a specific
                        tool within the Laravel ecosystem.
                    </p>

                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-base">
                                <Code2 class="h-4 w-4" />
                                Skill Markdown Format
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="rounded-lg bg-muted p-4">
                                <pre class="text-sm font-mono text-foreground leading-relaxed">---
name: Laravel Eloquent Expert
description: Best practices for Eloquent ORM
compatible_agents:
  - Claude Code
  - Cursor
tags:
  - eloquent
  - database
  - models
---

# Laravel Eloquent Expert

## Context
You are working with Laravel's Eloquent ORM...

## Rules
- Always use mass assignment protection
- Prefer query scopes for reusable queries
- Use eager loading to avoid N+1 problems

## Examples
### Creating a model with relationships
```php
class Post extends Model
{
    protected $fillable = ['title', 'body'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```</pre>
                            </div>
                        </CardContent>
                    </Card>

                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-foreground">What to Include</h3>
                        <ul class="list-inside list-disc space-y-2 text-muted-foreground">
                            <li>
                                <strong class="text-foreground">Context</strong> --
                                What is the scope of this skill? What tools/packages does it cover?
                            </li>
                            <li>
                                <strong class="text-foreground">Rules</strong> --
                                Clear, actionable instructions the AI should follow.
                            </li>
                            <li>
                                <strong class="text-foreground">Examples</strong> --
                                Real code samples showing the correct approach.
                            </li>
                            <li>
                                <strong class="text-foreground">Anti-patterns</strong> --
                                Common mistakes to avoid (optional but helpful).
                            </li>
                            <li>
                                <strong class="text-foreground">References</strong> --
                                Links to official docs or relevant resources.
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-foreground">How to Submit</h3>
                        <ol class="list-inside list-decimal space-y-2 text-muted-foreground">
                            <li>Create an account or log in.</li>
                            <li>
                                Go to the
                                <Link href="/submit" class="text-primary hover:underline">submission page</Link>.
                            </li>
                            <li>Fill in the skill details and paste your Markdown content.</li>
                            <li>Select compatible agents and add relevant tags.</li>
                            <li>Submit for review. We will review and publish your skill promptly.</li>
                        </ol>
                    </div>

                    <div class="flex justify-center pt-2">
                        <Link href="/submit">
                            <Button size="lg" class="gap-2">
                                <Pencil class="h-4 w-4" />
                                Submit a Skill
                            </Button>
                        </Link>
                    </div>
                </div>
            </section>

            <Separator class="mb-12" />

            <!-- Supported Agents -->
            <section class="mb-12">
                <div class="mb-6 flex items-center gap-2">
                    <Zap class="h-6 w-6 text-primary" />
                    <h2 class="text-2xl font-bold text-foreground">Supported Agents</h2>
                </div>

                <p class="mb-6 text-muted-foreground">
                    LaravelSkills supports the most popular AI coding assistants. Skills are designed to
                    work across agents, with format adaptations as needed.
                </p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <Card v-for="agent in agents" :key="agent.name">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Badge variant="default">{{ agent.name }}</Badge>
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <p class="text-sm text-muted-foreground">
                                {{ agent.description }}
                            </p>
                            <div class="rounded-md bg-muted px-3 py-2">
                                <code class="text-xs font-mono text-foreground">
                                    {{ agent.installPrefix }} &lt;skill-name&gt;
                                </code>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </section>
        </div>
    </div>
</template>
