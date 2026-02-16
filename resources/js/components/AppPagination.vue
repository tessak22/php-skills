<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Props = {
    links: PaginationLink[];
    from?: number;
    to?: number;
    total?: number;
};

defineProps<Props>();

function cleanLabel(label: string): string {
    return label
        .replace('&laquo;', '')
        .replace('&raquo;', '')
        .trim();
}
</script>

<template>
    <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
        <p v-if="from && to && total" class="text-sm text-muted-foreground">
            Showing {{ from }} to {{ to }} of {{ total }} results
        </p>
        <nav v-if="links.length > 3" class="flex items-center gap-1">
            <template v-for="(link, index) in links" :key="index">
                <!-- Previous button -->
                <Button
                    v-if="index === 0"
                    variant="outline"
                    size="icon"
                    as-child
                    :disabled="!link.url"
                    class="h-9 w-9"
                >
                    <Link v-if="link.url" :href="link.url" preserve-scroll>
                        <ChevronLeft class="h-4 w-4" />
                    </Link>
                    <span v-else>
                        <ChevronLeft class="h-4 w-4" />
                    </span>
                </Button>

                <!-- Next button -->
                <Button
                    v-else-if="index === links.length - 1"
                    variant="outline"
                    size="icon"
                    as-child
                    :disabled="!link.url"
                    class="h-9 w-9"
                >
                    <Link v-if="link.url" :href="link.url" preserve-scroll>
                        <ChevronRight class="h-4 w-4" />
                    </Link>
                    <span v-else>
                        <ChevronRight class="h-4 w-4" />
                    </span>
                </Button>

                <!-- Page numbers -->
                <Button
                    v-else
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                    as-child
                    class="h-9 min-w-9"
                >
                    <Link v-if="link.url && !link.active" :href="link.url" preserve-scroll>
                        {{ cleanLabel(link.label) }}
                    </Link>
                    <span v-else>
                        {{ cleanLabel(link.label) }}
                    </span>
                </Button>
            </template>
        </nav>
    </div>
</template>
