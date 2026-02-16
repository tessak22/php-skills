<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ArrowDownWideNarrow, Clock, Download } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AppPagination from '@/components/AppPagination.vue';
import CategoryFilter from '@/components/CategoryFilter.vue';
import SearchBar from '@/components/SearchBar.vue';
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
};

const props = defineProps<Props>();

const search = ref(props.filters.search ?? '');
const selectedCategory = ref(props.filters.category);
const currentSort = ref(props.filters.sort ?? 'newest');

function applyFilters() {
    const params: Record<string, string> = {};

    if (search.value) {
        params.search = search.value;
    }
    if (selectedCategory.value) {
        params.category = selectedCategory.value;
    }
    if (currentSort.value && currentSort.value !== 'newest') {
        params.sort = currentSort.value;
    }

    router.get('/skills', params, {
        preserveState: true,
        preserveScroll: true,
    });
}

function onSearch(value: string) {
    search.value = value;
    applyFilters();
}

function onCategorySelect(slug: string | undefined) {
    selectedCategory.value = slug;
    applyFilters();
}

function setSort(sort: string) {
    currentSort.value = sort;
    applyFilters();
}

watch(
    () => props.filters,
    (newFilters) => {
        search.value = newFilters.search ?? '';
        selectedCategory.value = newFilters.category;
        currentSort.value = newFilters.sort ?? 'newest';
    },
);
</script>

<template>
    <Head title="Skills Directory - LaravelSkills" />

    <div class="px-4 py-8">
        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    Skills Directory
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Browse and discover AI agent skills for Laravel and PHP development.
                </p>
            </div>

            <!-- Search -->
            <div class="mb-6">
                <SearchBar
                    v-model:model-value="search"
                    placeholder="Search skills..."
                    @search="onSearch"
                />
            </div>

            <!-- Filters Row -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <!-- Category Filter -->
                <CategoryFilter
                    :categories="categories"
                    :selected="selectedCategory"
                    @select="onCategorySelect"
                />

                <!-- Sort Buttons -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-muted-foreground">
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

            <!-- Skills Grid -->
            <div v-if="skills.data.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <SkillCard
                    v-for="skill in skills.data"
                    :key="skill.slug"
                    :skill="skill"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="py-16 text-center">
                <p class="text-lg font-medium text-foreground">No skills found</p>
                <p class="mt-2 text-muted-foreground">
                    Try adjusting your search or filters.
                </p>
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
    </div>
</template>
