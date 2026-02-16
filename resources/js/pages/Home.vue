<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Sparkles, Users } from 'lucide-vue-next';
import FeedPost from '@/components/FeedPost.vue';
import SkillCard from '@/components/SkillCard.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type Skill = {
    name: string;
    slug: string;
    description: string;
    install_count: number;
    is_featured: boolean;
    is_official: boolean;
    compatible_agents: string[];
    tags: string[];
    author?: {
        name: string;
        slug: string;
        avatar_url?: string;
    };
    category?: {
        name: string;
        slug: string;
    };
};

type Post = {
    id: string;
    platform: 'x' | 'bluesky' | 'youtube' | 'devto';
    author_name: string;
    author_handle: string;
    author_avatar_url?: string;
    content: string;
    media_url?: string;
    post_url: string;
    engagement_score: number;
    is_featured: boolean;
    published_at: string;
};

type Category = {
    id: string;
    name: string;
    slug: string;
};

type Props = {
    featuredSkills: Skill[];
    recentPosts: Post[];
    categories: Category[];
};

defineProps<Props>();
</script>

<template>
    <Head title="LaravelSkills - Discover AI Skills for Laravel" />

    <div class="flex flex-col">
        <!-- Hero Section -->
        <section class="relative overflow-hidden border-b bg-gradient-to-b from-primary/5 to-transparent px-4 py-16 md:py-24">
            <div class="mx-auto max-w-7xl text-center">
                <div class="mx-auto max-w-3xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border bg-background px-4 py-1.5 text-sm text-muted-foreground">
                        <Sparkles class="h-4 w-4 text-primary" />
                        AI-powered Laravel development
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-foreground md:text-6xl">
                        Discover AI Skills
                        <br />
                        <span class="text-primary">for Laravel</span>
                    </h1>
                    <p class="mt-6 text-lg leading-relaxed text-muted-foreground md:text-xl">
                        A curated directory of reusable AI agent skills for Laravel and PHP.
                        Install skills for Claude Code, Cursor, Windsurf, and more.
                        Built by the community, for the community.
                    </p>
                    <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link href="/skills">
                            <Button size="lg" class="gap-2">
                                Browse Skills
                                <ArrowRight class="h-4 w-4" />
                            </Button>
                        </Link>
                        <Link href="/submit">
                            <Button variant="outline" size="lg">
                                Submit a Skill
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Skills Section -->
        <section class="px-4 py-12 md:py-16">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
                            Featured Skills
                        </h2>
                        <p class="mt-2 text-muted-foreground">
                            Hand-picked skills to supercharge your Laravel workflow.
                        </p>
                    </div>
                    <Link href="/skills" class="hidden sm:block">
                        <Button variant="ghost" class="gap-2">
                            Browse all
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <SkillCard
                        v-for="skill in featuredSkills"
                        :key="skill.slug"
                        :skill="skill"
                    />
                </div>
                <div class="mt-6 text-center sm:hidden">
                    <Link href="/skills">
                        <Button variant="outline" class="gap-2">
                            Browse all skills
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Community Feed Section -->
        <section class="border-t bg-muted/30 px-4 py-12 md:py-16">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <Users class="h-5 w-5 text-primary" />
                            <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
                                Community Feed
                            </h2>
                        </div>
                        <p class="text-muted-foreground">
                            What the Laravel community is building with AI.
                        </p>
                    </div>
                    <Link href="/feed" class="hidden sm:block">
                        <Button variant="ghost" class="gap-2">
                            View all
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <FeedPost
                        v-for="post in recentPosts"
                        :key="post.id"
                        :post="post"
                    />
                </div>
                <div class="mt-6 text-center sm:hidden">
                    <Link href="/feed">
                        <Button variant="outline" class="gap-2">
                            View all posts
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
