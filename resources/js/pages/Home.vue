<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ArrowDownWideNarrow, Clock, Download, SearchX, Sparkles, Users } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import AppPagination from '@/components/AppPagination.vue';
import CopyButton from '@/components/CopyButton.vue';
import FeedPost from '@/components/FeedPost.vue';
import FeedPostSkeleton from '@/components/FeedPostSkeleton.vue';
import SearchBar from '@/components/SearchBar.vue';
import SkillCard from '@/components/SkillCard.vue';
import SkillCardSkeleton from '@/components/SkillCardSkeleton.vue';
import { Badge } from '@/components/ui/badge';
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

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedSkills = {
    data: Skill[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
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

type Filters = {
    search?: string;
    category?: string;
    sort?: string;
};

type Props = {
    skills: PaginatedSkills;
    categories: Category[];
    filters: Filters;
    recentPosts?: Post[];
};

const props = defineProps<Props>();

const search = ref(props.filters.search ?? '');
const selectedCategory = ref(props.filters.category);
const currentSort = ref(props.filters.sort ?? 'installs');
const loading = ref(false);
const selectedPlatform = ref<string | null>(null);

let removeStartListener: (() => void) | null = null;
let removeFinishListener: (() => void) | null = null;

onMounted(() => {
    removeStartListener = router.on('start', () => {
        loading.value = true;
    });
    removeFinishListener = router.on('finish', () => {
        loading.value = false;
    });
});

onUnmounted(() => {
    removeStartListener?.();
    removeFinishListener?.();
});

function applyFilters() {
    const params: Record<string, string> = {};

    if (search.value) {
        params.search = search.value;
    }
    if (selectedCategory.value) {
        params.category = selectedCategory.value;
    }
    if (currentSort.value && currentSort.value !== 'installs') {
        params.sort = currentSort.value;
    }

    router.get('/', params, {
        preserveState: true,
        preserveScroll: true,
    });
}

function onSearch(value: string) {
    search.value = value;
    applyFilters();
}

function setSort(sort: string) {
    currentSort.value = sort;
    applyFilters();
}

const filteredPosts = computed(() => {
    if (!props.recentPosts) return [];
    if (!selectedPlatform.value) return props.recentPosts;
    return props.recentPosts.filter(post => post.platform === selectedPlatform.value);
});

const platforms = [
    { value: null, label: 'All' },
    { value: 'x', label: 'X' },
    { value: 'bluesky', label: 'Bluesky' },
    { value: 'youtube', label: 'YouTube' },
    { value: 'devto', label: 'DEV.to' },
    { value: 'community', label: 'Community' },
];

watch(
    () => props.filters,
    (newFilters) => {
        search.value = newFilters.search ?? '';
        selectedCategory.value = newFilters.category;
        currentSort.value = newFilters.sort ?? 'installs';
    },
);
</script>

<template>
    <Head title="Laravel Skills - AI Agent Skills for Laravel">
        <meta name="description" content="An open directory of reusable AI agent skills for Laravel and PHP. Install skills for Claude Code, Cursor, Windsurf, and more." />
        <meta property="og:title" content="Laravel Skills - AI Agent Skills for Laravel" />
        <meta property="og:description" content="An open directory of reusable AI agent skills for Laravel and PHP. Install skills for Claude Code, Cursor, Windsurf, and more." />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Laravel Skills - AI Agent Skills for Laravel" />
        <meta name="twitter:description" content="An open directory of reusable AI agent skills for Laravel and PHP. Install skills for Claude Code, Cursor, Windsurf, and more." />
    </Head>

    <div class="flex flex-col">
        <!-- Hero Section -->
        <section class="relative overflow-hidden border-b bg-gradient-to-br from-primary/10 via-primary/5 to-transparent px-4 py-20 md:py-28">
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
                        AI Skills
                        <br />
                        <span class="bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent">for Laravel</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-muted-foreground md:text-xl">
                        An open directory of reusable AI agent skills for Laravel and PHP. Install with a single command.
                    </p>
                    <div class="mx-auto mt-10 max-w-lg">
                        <CopyButton text="npx skills add <owner/repo>" label="Copy install command" />
                    </div>
                    <p class="mt-4 text-sm text-muted-foreground">
                        Works with Claude Code, Cursor, Windsurf, Copilot, and more.
                    </p>
                </div>
            </div>
        </section>

        <!-- Skills Directory Section -->
        <section class="border-t px-4 py-16 md:py-20">
            <div class="mx-auto max-w-7xl">
                <!-- Search -->
                <div class="mb-6">
                    <SearchBar
                        v-model:model-value="search"
                        placeholder="Search skills..."
                        @search="onSearch"
                    />
                </div>

                <!-- Sort Buttons -->
                <div class="mb-8 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-foreground">
                        Skills
                        <span v-if="skills.total" class="ml-2 text-sm font-normal text-muted-foreground">
                            {{ skills.total }} total
                        </span>
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="hidden text-sm text-muted-foreground sm:inline">
                            <ArrowDownWideNarrow class="mr-1 inline h-4 w-4" />
                            Sort:
                        </span>
                        <Button
                            size="sm"
                            :variant="currentSort === 'newest' ? 'default' : 'outline'"
                            class="gap-1.5"
                            @click="setSort('newest')"
                        >
                            <Clock class="h-3.5 w-3.5" />
                            Newest
                        </Button>
                        <Button
                            size="sm"
                            :variant="currentSort === 'installs' ? 'default' : 'outline'"
                            class="gap-1.5"
                            @click="setSort('installs')"
                        >
                            <Download class="h-3.5 w-3.5" />
                            Most Installed
                        </Button>
                    </div>
                </div>

                <!-- Skeleton Loading State -->
                <div v-if="loading" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <SkillCardSkeleton v-for="n in 6" :key="n" />
                </div>

                <!-- Skills Grid -->
                <div v-else-if="skills.data.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <SkillCard
                        v-for="skill in skills.data"
                        :key="skill.slug"
                        :skill="skill"
                    />
                </div>

                <!-- Empty State -->
                <div v-else class="rounded-lg border border-dashed py-16 text-center">
                    <SearchX class="mx-auto h-10 w-10 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-medium text-foreground">No skills found</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm text-muted-foreground">
                        No skills match your current search. Try broadening your search terms.
                    </p>
                    <div class="mt-6">
                        <Button
                            v-if="search || selectedCategory"
                            variant="outline"
                            size="sm"
                            @click="search = ''; selectedCategory = undefined; applyFilters()"
                        >
                            Clear filters
                        </Button>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    <AppPagination
                        :links="skills.links"
                        :from="skills.from"
                        :to="skills.to"
                        :total="skills.total"
                    />
                </div>
            </div>
        </section>

        <!-- Community Feed Section -->
        <section class="border-t bg-muted/30 px-4 py-16 md:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8">
                    <div class="mb-2 flex items-center gap-2">
                        <Users class="h-5 w-5 text-primary" />
                        <span class="text-sm font-medium uppercase tracking-wider text-primary">Community</span>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
                        Community Feed
                    </h2>
                    <p class="mt-2 max-w-lg text-muted-foreground">
                        Celebrating developers building with Laravel and AI.
                    </p>
                </div>

                <!-- Platform Tabs -->
                <div class="mb-6 flex flex-wrap gap-2">
                    <Badge
                        v-for="platform in platforms"
                        :key="platform.label"
                        variant="outline"
                        class="cursor-pointer px-3 py-1.5 text-sm transition-colors hover:bg-accent"
                        :class="selectedPlatform === platform.value ? 'bg-primary text-primary-foreground hover:bg-primary/90' : ''"
                        @click="selectedPlatform = platform.value"
                    >
                        {{ platform.label }}
                    </Badge>
                </div>

                <!-- Deferred Feed Content -->
                <Deferred data="recentPosts">
                    <template #fallback>
                        <div class="grid gap-4 md:grid-cols-2">
                            <FeedPostSkeleton v-for="n in 4" :key="n" />
                        </div>
                    </template>

                    <div v-if="filteredPosts.length" class="grid gap-4 md:grid-cols-2">
                        <FeedPost
                            v-for="post in filteredPosts"
                            :key="post.id"
                            :post="post"
                        />
                    </div>

                    <div v-else class="rounded-lg border border-dashed py-16 text-center">
                        <Users class="mx-auto h-10 w-10 text-muted-foreground/50" />
                        <h3 class="mt-4 text-lg font-medium text-foreground">No posts yet</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Community content from X, Bluesky, YouTube, and DEV.to will appear here soon.
                        </p>
                    </div>
                </Deferred>
            </div>
        </section>
    </div>
</template>
