<script setup lang="ts">
import { ExternalLink } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';

type Props = {
    post: {
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
};

defineProps<Props>();

const platformLabels: Record<string, string> = {
    x: 'X',
    bluesky: 'Bluesky',
    youtube: 'YouTube',
    devto: 'DEV.to',
};

const platformColors: Record<string, string> = {
    x: 'bg-black text-white dark:bg-white dark:text-black',
    bluesky: 'bg-blue-500 text-white',
    youtube: 'bg-red-600 text-white',
    devto: 'bg-neutral-800 text-white',
};

function formatDate(dateStr: string): string {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays}d ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)}w ago`;
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}
</script>

<template>
    <Card class="transition-shadow hover:shadow-md">
        <CardContent class="p-4">
            <div class="flex gap-3">
                <img
                    v-if="post.author_avatar_url"
                    :src="post.author_avatar_url"
                    :alt="post.author_name"
                    class="h-10 w-10 shrink-0 rounded-full"
                />
                <div
                    v-else
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-muted text-sm font-semibold text-muted-foreground"
                >
                    {{ post.author_name.charAt(0) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="truncate text-sm font-semibold">{{ post.author_name }}</span>
                        <span class="truncate text-xs text-muted-foreground">{{ post.author_handle }}</span>
                        <Badge :class="platformColors[post.platform]" class="ml-auto shrink-0 text-xs">
                            {{ platformLabels[post.platform] }}
                        </Badge>
                    </div>
                    <p class="mt-1.5 text-sm leading-relaxed text-foreground">
                        {{ post.content }}
                    </p>
                    <img
                        v-if="post.media_url"
                        :src="post.media_url"
                        alt="Post media"
                        class="mt-3 max-h-48 rounded-lg object-cover"
                    />
                    <div class="mt-3 flex items-center justify-between text-xs text-muted-foreground">
                        <span>{{ formatDate(post.published_at) }}</span>
                        <a
                            :href="post.post_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-1 text-primary hover:underline"
                        >
                            View original
                            <ExternalLink class="h-3 w-3" />
                        </a>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
