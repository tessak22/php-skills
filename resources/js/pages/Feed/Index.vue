<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { MessageSquareOff } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';
import AppPagination from '@/components/AppPagination.vue';
import FeedPost from '@/components/FeedPost.vue';
import FeedPostSkeleton from '@/components/FeedPostSkeleton.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

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

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedPosts = {
    data: Post[];
    links: PaginationLink[];
    from: number;
    to: number;
    total: number;
};

type Props = {
    posts: PaginatedPosts;
    currentPlatform: string | null;
};

const props = defineProps<Props>();

const loading = ref(false);

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

type PlatformTab = {
    label: string;
    value: string | null;
};

const platforms: PlatformTab[] = [
    { label: 'All', value: null },
    { label: 'X', value: 'x' },
    { label: 'Bluesky', value: 'bluesky' },
    { label: 'YouTube', value: 'youtube' },
    { label: 'DEV.to', value: 'devto' },
];

function selectPlatform(platform: string | null) {
    const params: Record<string, string> = {};
    if (platform) {
        params.platform = platform;
    }
    router.get('/feed', params, {
        preserveState: true,
        preserveScroll: true,
    });
}

function isActive(platform: string | null): boolean {
    return props.currentPlatform === platform;
}
</script>

<template>
    <Head title="Community Feed - LaravelSkills">
        <meta name="description" content="See what the Laravel community is building, sharing, and discussing around AI-powered development. Aggregated from X, Bluesky, YouTube, and DEV.to." />
        <meta property="og:title" content="Community Feed - LaravelSkills" />
        <meta property="og:description" content="See what the Laravel community is building, sharing, and discussing around AI-powered development. Aggregated from X, Bluesky, YouTube, and DEV.to." />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Community Feed - LaravelSkills" />
        <meta name="twitter:description" content="See what the Laravel community is building, sharing, and discussing around AI-powered development. Aggregated from X, Bluesky, YouTube, and DEV.to." />
    </Head>

    <div class="px-4 py-8">
        <div class="mx-auto max-w-3xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    Community Feed
                </h1>
                <p class="mt-2 text-muted-foreground">
                    What the Laravel community is building, sharing, and discussing around AI-powered development.
                </p>
            </div>

            <!-- Platform Filters -->
            <div class="mb-8 flex flex-wrap gap-2">
                <Button
                    v-for="platform in platforms"
                    :key="platform.label"
                    size="sm"
                    :variant="isActive(platform.value) ? 'default' : 'outline'"
                    @click="selectPlatform(platform.value)"
                >
                    {{ platform.label }}
                </Button>
            </div>

            <!-- Skeleton Loading State -->
            <div v-if="loading" class="space-y-4">
                <FeedPostSkeleton v-for="n in 4" :key="n" />
            </div>

            <!-- Posts List -->
            <div v-else-if="posts.data.length" class="space-y-4">
                <FeedPost
                    v-for="post in posts.data"
                    :key="post.id"
                    :post="post"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="rounded-lg border border-dashed py-16 text-center">
                <MessageSquareOff class="mx-auto h-10 w-10 text-muted-foreground/50" />
                <h3 class="mt-4 text-lg font-medium text-foreground">No posts found</h3>
                <p class="mt-2 max-w-md mx-auto text-sm text-muted-foreground">
                    <template v-if="currentPlatform">
                        No posts found for this platform. Try selecting a different platform or view all posts.
                    </template>
                    <template v-else>
                        Community content from X, Bluesky, YouTube, and DEV.to will appear here as it is collected.
                    </template>
                </p>
                <div v-if="currentPlatform" class="mt-6">
                    <Button
                        variant="outline"
                        size="sm"
                        @click="selectPlatform(null)"
                    >
                        View all platforms
                    </Button>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                <AppPagination
                    :links="posts.links"
                    :from="posts.from"
                    :to="posts.to"
                    :total="posts.total"
                />
            </div>
        </div>
    </div>
</template>
