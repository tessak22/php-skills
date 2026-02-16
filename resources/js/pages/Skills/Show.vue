<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Download, ExternalLink, Github, Star, Tag, Verified } from 'lucide-vue-next';
import CopyButton from '@/components/CopyButton.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Author = {
    name: string;
    slug: string;
    github_username?: string;
    avatar_url?: string;
    bio?: string;
};

type Category = {
    id: string;
    name: string;
    slug: string;
};

type Skill = {
    name: string;
    slug: string;
    description: string;
    install_command: string;
    content: string;
    install_count: number;
    is_featured: boolean;
    is_official: boolean;
    compatible_agents: string[];
    tags: string[];
    source_url?: string;
    author?: Author;
    category?: Category;
    created_at: string;
    updated_at: string;
};

type Props = {
    skill: Skill;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Home', href: '/' },
    { title: 'Skills', href: '/skills' },
    { title: props.skill.name },
];

function formatCount(count: number): string {
    if (count >= 1000) {
        return `${(count / 1000).toFixed(1)}k`;
    }
    return count.toString();
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function getInitial(name: string): string {
    return name.charAt(0).toUpperCase();
}
</script>

<template>
    <PublicLayout :breadcrumbs="breadcrumbs">
        <Head :title="`${skill.name} - LaravelSkills`" />

        <div class="px-4 py-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-8 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <!-- Skill Header -->
                        <div class="mb-6">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                                    {{ skill.name }}
                                </h1>
                                <Badge v-if="skill.is_official" variant="default" class="gap-1">
                                    <Verified class="h-3 w-3" />
                                    Official
                                </Badge>
                                <Badge v-if="skill.is_featured" variant="secondary" class="gap-1">
                                    <Star class="h-3 w-3 fill-yellow-500 text-yellow-500" />
                                    Featured
                                </Badge>
                            </div>
                            <p class="mt-3 text-lg text-muted-foreground">
                                {{ skill.description }}
                            </p>
                        </div>

                        <!-- Install Command -->
                        <div class="mb-8">
                            <h2 class="mb-2 text-sm font-medium text-foreground">Install</h2>
                            <CopyButton :text="skill.install_command" />
                        </div>

                        <!-- Meta Info Bar -->
                        <div class="mb-8 flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                            <div class="flex items-center gap-1.5">
                                <Download class="h-4 w-4" />
                                <span>{{ formatCount(skill.install_count) }} installs</span>
                            </div>
                            <Separator orientation="vertical" class="h-4" />
                            <span>Updated {{ formatDate(skill.updated_at) }}</span>
                            <template v-if="skill.source_url">
                                <Separator orientation="vertical" class="h-4" />
                                <a
                                    :href="skill.source_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center gap-1 text-primary hover:underline"
                                >
                                    Source
                                    <ExternalLink class="h-3 w-3" />
                                </a>
                            </template>
                        </div>

                        <Separator class="mb-8" />

                        <!-- Skill Content -->
                        <div class="prose prose-neutral dark:prose-invert max-w-none">
                            <!-- eslint-disable-next-line vue/no-v-html -->
                            <div v-html="skill.content" />
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Author Card -->
                        <Card v-if="skill.author">
                            <CardHeader>
                                <CardTitle class="text-sm font-medium text-muted-foreground">Author</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="flex items-start gap-3">
                                    <Avatar class="h-10 w-10">
                                        <AvatarImage
                                            v-if="skill.author.avatar_url"
                                            :src="skill.author.avatar_url"
                                            :alt="skill.author.name"
                                        />
                                        <AvatarFallback class="bg-primary/10 text-primary">
                                            {{ getInitial(skill.author.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-foreground">
                                            {{ skill.author.name }}
                                        </p>
                                        <a
                                            v-if="skill.author.github_username"
                                            :href="`https://github.com/${skill.author.github_username}`"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="mt-1 flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
                                        >
                                            <Github class="h-3.5 w-3.5" />
                                            {{ skill.author.github_username }}
                                        </a>
                                        <p
                                            v-if="skill.author.bio"
                                            class="mt-2 text-sm text-muted-foreground"
                                        >
                                            {{ skill.author.bio }}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Category -->
                        <Card v-if="skill.category">
                            <CardHeader>
                                <CardTitle class="text-sm font-medium text-muted-foreground">Category</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Link
                                    :href="`/skills?category=${skill.category.slug}`"
                                    class="inline-block"
                                >
                                    <Badge variant="outline" class="cursor-pointer text-sm hover:bg-accent">
                                        {{ skill.category.name }}
                                    </Badge>
                                </Link>
                            </CardContent>
                        </Card>

                        <!-- Compatible Agents -->
                        <Card v-if="skill.compatible_agents?.length">
                            <CardHeader>
                                <CardTitle class="text-sm font-medium text-muted-foreground">Compatible Agents</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="flex flex-wrap gap-2">
                                    <Badge
                                        v-for="agent in skill.compatible_agents"
                                        :key="agent"
                                        variant="secondary"
                                    >
                                        {{ agent }}
                                    </Badge>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Tags -->
                        <Card v-if="skill.tags?.length">
                            <CardHeader>
                                <CardTitle class="text-sm font-medium text-muted-foreground">
                                    <Tag class="mr-1 inline h-4 w-4" />
                                    Tags
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="flex flex-wrap gap-2">
                                    <Badge
                                        v-for="tag in skill.tags"
                                        :key="tag"
                                        variant="outline"
                                    >
                                        {{ tag }}
                                    </Badge>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
