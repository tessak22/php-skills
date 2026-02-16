<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppPagination from '@/components/AppPagination.vue';
import FeedPost from '@/components/FeedPost.vue';
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
    <Head title="Community Feed - LaravelSkills" />

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

            <!-- Posts List -->
            <div v-if="posts.data.length" class="space-y-4">
                <FeedPost
                    v-for="post in posts.data"
                    :key="post.id"
                    :post="post"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="py-16 text-center">
                <p class="text-lg font-medium text-foreground">No posts found</p>
                <p class="mt-2 text-muted-foreground">
                    Try selecting a different platform filter.
                </p>
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
