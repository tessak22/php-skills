<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, Rocket, Sparkles, Users } from 'lucide-vue-next';
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
    <Head title="LaravelSkills - AI Agent Skills for Laravel">
        <meta name="description" content="A curated directory of reusable AI agent skills for Laravel and PHP. Discover, install, and share skills for Claude Code, Cursor, Windsurf, and more." />
        <meta property="og:title" content="LaravelSkills - AI Agent Skills for Laravel" />
        <meta property="og:description" content="A curated directory of reusable AI agent skills for Laravel and PHP. Discover, install, and share skills for Claude Code, Cursor, Windsurf, and more." />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="LaravelSkills - AI Agent Skills for Laravel" />
        <meta name="twitter:description" content="A curated directory of reusable AI agent skills for Laravel and PHP. Discover, install, and share skills for Claude Code, Cursor, Windsurf, and more." />
    </Head>

    <div class="flex flex-col">
        <!-- Hero Section -->
        <section class="relative overflow-hidden border-b bg-gradient-to-br from-primary/10 via-primary/5 to-transparent px-4 py-20 md:py-32">
            <!-- Decorative background elements -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-primary/5 blur-3xl" />
                <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-primary/5 blur-3xl" />
            </div>

            <div class="relative mx-auto max-w-7xl text-center">
                <div class="mx-auto max-w-3xl">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border bg-background/80 px-4 py-1.5 text-sm text-muted-foreground backdrop-blur-sm">
                        <Sparkles class="h-4 w-4 text-primary" />
                        AI-powered Laravel development
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl md:text-6xl lg:text-7xl">
                        Discover AI Skills
                        <br />
                        <span class="bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent">for Laravel</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-muted-foreground md:text-xl">
                        A curated directory of reusable AI agent skills for Laravel and PHP.
                        Install skills for Claude Code, Cursor, Windsurf, and more.
                        Built by the community, for the community.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link href="/skills">
                            <Button size="lg" class="gap-2 px-8">
                                Browse Skills
                                <ArrowRight class="h-4 w-4" />
                            </Button>
                        </Link>
                        <Link href="/submit">
                            <Button variant="outline" size="lg" class="px-8">
                                Submit a Skill
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Skills Section -->
        <section class="px-4 py-16 md:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <Rocket class="h-5 w-5 text-primary" />
                            <span class="text-sm font-medium uppercase tracking-wider text-primary">Curated</span>
                        </div>
                        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
                            Featured Skills
                        </h2>
                        <p class="mt-2 max-w-lg text-muted-foreground">
                            Hand-picked skills to supercharge your Laravel workflow. Each one is reviewed for quality and relevance.
                        </p>
                    </div>
                    <Link href="/skills" class="hidden sm:block">
                        <Button variant="ghost" class="gap-2">
                            Browse all
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                </div>

                <!-- Featured Skills Grid -->
                <div v-if="featuredSkills.length" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <SkillCard
                        v-for="skill in featuredSkills"
                        :key="skill.slug"
                        :skill="skill"
                    />
                </div>

                <!-- Empty State: No Featured Skills -->
                <div v-else class="rounded-lg border border-dashed py-16 text-center">
                    <Sparkles class="mx-auto h-10 w-10 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-medium text-foreground">Skills coming soon</h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        We are curating the best AI agent skills for Laravel. Check back shortly.
                    </p>
                    <div class="mt-6">
                        <Link href="/submit">
                            <Button variant="outline" size="sm" class="gap-2">
                                Be the first to contribute
                                <ArrowRight class="h-4 w-4" />
                            </Button>
                        </Link>
                    </div>
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
        <section class="border-t bg-muted/30 px-4 py-16 md:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <Users class="h-5 w-5 text-primary" />
                            <span class="text-sm font-medium uppercase tracking-wider text-primary">Community</span>
                        </div>
                        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
                            Community Feed
                        </h2>
                        <p class="mt-2 max-w-lg text-muted-foreground">
                            See what the Laravel community is building, sharing, and discussing around AI-powered development.
                        </p>
                    </div>
                    <Link href="/feed" class="hidden sm:block">
                        <Button variant="ghost" class="gap-2">
                            View all
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                </div>

                <!-- Recent Posts Grid -->
                <div v-if="recentPosts.length" class="grid gap-4 md:grid-cols-2">
                    <FeedPost
                        v-for="post in recentPosts"
                        :key="post.id"
                        :post="post"
                    />
                </div>

                <!-- Empty State: No Recent Posts -->
                <div v-else class="rounded-lg border border-dashed py-16 text-center">
                    <BookOpen class="mx-auto h-10 w-10 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-medium text-foreground">No posts yet</h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Community content from X, Bluesky, YouTube, and DEV.to will appear here soon.
                    </p>
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
