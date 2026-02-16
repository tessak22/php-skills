<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type Category = {
    id: string;
    name: string;
    slug: string;
};

type Props = {
    categories: Category[];
};

defineProps<Props>();

const compatibleAgentOptions = [
    { value: 'Claude Code', label: 'Claude Code' },
    { value: 'Cursor', label: 'Cursor' },
    { value: 'Windsurf', label: 'Windsurf' },
    { value: 'Copilot', label: 'Copilot' },
];

const form = useForm({
    name: '',
    description: '',
    content: '',
    install_command: '',
    category_id: '',
    compatible_agents: [] as string[],
    tags: '',
    source_url: '',
});

function toggleAgent(agent: string) {
    const index = form.compatible_agents.indexOf(agent);
    if (index === -1) {
        form.compatible_agents.push(agent);
    } else {
        form.compatible_agents.splice(index, 1);
    }
}

function submit() {
    form.post('/api/v1/skills', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Submit a Skill - LaravelSkills" />

    <div class="px-4 py-8">
        <div class="mx-auto max-w-2xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    Submit a Skill
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Share your AI agent skill with the Laravel community.
                    Your submission will be reviewed before appearing in the directory.
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Skill Details</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Name -->
                        <div class="space-y-2">
                            <Label for="name">Name *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Laravel Eloquent Expert"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Description *</Label>
                            <Input
                                id="description"
                                v-model="form.description"
                                type="text"
                                placeholder="A short description of what this skill does"
                                required
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <!-- Install Command -->
                        <div class="space-y-2">
                            <Label for="install_command">Install Command *</Label>
                            <Input
                                id="install_command"
                                v-model="form.install_command"
                                type="text"
                                placeholder="e.g., claude skill install laravel-eloquent"
                                class="font-mono"
                                required
                            />
                            <InputError :message="form.errors.install_command" />
                        </div>

                        <!-- Content -->
                        <div class="space-y-2">
                            <Label for="content">Skill Content (Markdown) *</Label>
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="12"
                                placeholder="Write the full skill content in Markdown format..."
                                required
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm font-mono ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="form.errors.content" />
                        </div>

                        <!-- Category -->
                        <div class="space-y-2">
                            <Label for="category_id">Category</Label>
                            <select
                                id="category_id"
                                v-model="form.category_id"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Select a category</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.category_id" />
                        </div>

                        <!-- Compatible Agents -->
                        <div class="space-y-3">
                            <Label>Compatible Agents</Label>
                            <div class="grid grid-cols-2 gap-3">
                                <div
                                    v-for="agent in compatibleAgentOptions"
                                    :key="agent.value"
                                    class="flex items-center gap-2"
                                >
                                    <Checkbox
                                        :id="`agent-${agent.value}`"
                                        :checked="form.compatible_agents.includes(agent.value)"
                                        @update:checked="toggleAgent(agent.value)"
                                    />
                                    <Label
                                        :for="`agent-${agent.value}`"
                                        class="cursor-pointer text-sm font-normal"
                                    >
                                        {{ agent.label }}
                                    </Label>
                                </div>
                            </div>
                            <InputError :message="form.errors.compatible_agents" />
                        </div>

                        <!-- Tags -->
                        <div class="space-y-2">
                            <Label for="tags">Tags</Label>
                            <Input
                                id="tags"
                                v-model="form.tags"
                                type="text"
                                placeholder="eloquent, models, database (comma-separated)"
                            />
                            <p class="text-xs text-muted-foreground">
                                Separate tags with commas.
                            </p>
                            <InputError :message="form.errors.tags" />
                        </div>

                        <!-- Source URL -->
                        <div class="space-y-2">
                            <Label for="source_url">Source URL</Label>
                            <Input
                                id="source_url"
                                v-model="form.source_url"
                                type="url"
                                placeholder="https://github.com/username/repo"
                            />
                            <InputError :message="form.errors.source_url" />
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                                Skill submitted successfully!
                            </p>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Submitting...' : 'Submit Skill' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
