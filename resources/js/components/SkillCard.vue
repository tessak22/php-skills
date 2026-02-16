<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Download, Star } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';

type Props = {
    skill: {
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
};

defineProps<Props>();

function formatCount(count: number): string {
    if (count >= 1000) {
        return `${(count / 1000).toFixed(1)}k`;
    }
    return count.toString();
}
</script>

<template>
    <Card class="group relative flex flex-col transition-shadow hover:shadow-md">
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <Link :href="`/skills/${skill.slug}`" class="hover:underline">
                        <CardTitle class="truncate text-base">
                            {{ skill.name }}
                        </CardTitle>
                    </Link>
                    <p v-if="skill.category" class="mt-1 text-xs text-muted-foreground">
                        {{ skill.category.name }}
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-1">
                    <Star v-if="skill.is_featured" class="h-4 w-4 text-yellow-500 fill-yellow-500" />
                    <Badge v-if="skill.is_official" variant="default" class="text-xs">Official</Badge>
                </div>
            </div>
        </CardHeader>
        <CardContent class="flex-1 pb-3">
            <p class="line-clamp-2 text-sm text-muted-foreground">
                {{ skill.description }}
            </p>
            <div v-if="skill.tags?.length" class="mt-3 flex flex-wrap gap-1">
                <Badge
                    v-for="tag in skill.tags.slice(0, 3)"
                    :key="tag"
                    variant="secondary"
                    class="text-xs"
                >
                    {{ tag }}
                </Badge>
            </div>
        </CardContent>
        <CardFooter class="border-t pt-3 text-xs text-muted-foreground">
            <div class="flex w-full items-center justify-between">
                <div class="flex items-center gap-2">
                    <img
                        v-if="skill.author?.avatar_url"
                        :src="skill.author.avatar_url"
                        :alt="skill.author.name"
                        class="h-5 w-5 rounded-full"
                    />
                    <span v-if="skill.author">{{ skill.author.name }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <Download class="h-3.5 w-3.5" />
                    <span>{{ formatCount(skill.install_count) }}</span>
                </div>
            </div>
        </CardFooter>
    </Card>
</template>
