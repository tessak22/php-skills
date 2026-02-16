<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type Platform = {
    value: string;
    label: string;
};

type Props = {
    platforms: Platform[];
};

defineProps<Props>();

const form = useForm({
    content: '',
    post_url: '',
    platform: 'community',
    author_name: '',
    author_handle: '',
});

const page = usePage();

function submit() {
    if (!page.props.auth?.user) {
        router.visit('/login');
        return;
    }

    form.post('/api/v1/feed', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Share Content - LaravelSkills">
        <meta name="description" content="Share a blog post, video, tweet, or other content with the Laravel community." />
        <meta property="og:title" content="Share Content - LaravelSkills" />
        <meta property="og:description" content="Share a blog post, video, tweet, or other content with the Laravel community." />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Share Content - LaravelSkills" />
        <meta name="twitter:description" content="Share a blog post, video, tweet, or other content with the Laravel community." />
    </Head>

    <div class="px-4 py-8">
        <div class="mx-auto max-w-2xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    Share Content
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Share a blog post, video, tweet, or other content with the Laravel community.
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Content Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Content -->
                        <div class="space-y-2">
                            <Label for="content">What are you sharing? *</Label>
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="6"
                                placeholder="Describe the content you're sharing with the community..."
                                required
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="form.errors.content" />
                        </div>

                        <!-- URL -->
                        <div class="space-y-2">
                            <Label for="post_url">URL *</Label>
                            <Input
                                id="post_url"
                                v-model="form.post_url"
                                type="url"
                                placeholder="https://example.com/my-article"
                                required
                            />
                            <p class="text-xs text-muted-foreground">
                                Link to the blog post, video, tweet, or other content.
                            </p>
                            <InputError :message="form.errors.post_url" />
                        </div>

                        <!-- Platform -->
                        <div class="space-y-2">
                            <Label for="platform">Platform</Label>
                            <select
                                id="platform"
                                v-model="form.platform"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option
                                    v-for="platform in platforms"
                                    :key="platform.value"
                                    :value="platform.value"
                                >
                                    {{ platform.label }}
                                </option>
                            </select>
                            <p class="text-xs text-muted-foreground">
                                Select the platform where this content lives. Defaults to Community.
                            </p>
                            <InputError :message="form.errors.platform" />
                        </div>

                        <!-- Author Name (optional) -->
                        <div class="space-y-2">
                            <Label for="author_name">Author Name</Label>
                            <Input
                                id="author_name"
                                v-model="form.author_name"
                                type="text"
                                placeholder="Leave blank to use your account name"
                            />
                            <InputError :message="form.errors.author_name" />
                        </div>

                        <!-- Author Handle (optional) -->
                        <div class="space-y-2">
                            <Label for="author_handle">Author Handle</Label>
                            <Input
                                id="author_handle"
                                v-model="form.author_handle"
                                type="text"
                                placeholder="@handle (optional)"
                            />
                            <InputError :message="form.errors.author_handle" />
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                                Content shared successfully!
                            </p>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Sharing...' : 'Share Content' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
