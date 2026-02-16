<script setup lang="ts">
import { Badge } from '@/components/ui/badge';

type Category = {
    id: string;
    name: string;
    slug: string;
};

type Props = {
    categories: Category[];
    selected?: string;
};

type Emits = {
    select: [slug: string | undefined];
};

defineProps<Props>();
const emit = defineEmits<Emits>();

function toggle(slug: string, currentlySelected?: string) {
    emit('select', slug === currentlySelected ? undefined : slug);
}
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <Badge
            variant="outline"
            class="cursor-pointer transition-colors"
            :class="!selected ? 'bg-primary text-primary-foreground border-primary' : 'hover:bg-accent'"
            @click="emit('select', undefined)"
        >
            All
        </Badge>
        <Badge
            v-for="category in categories"
            :key="category.id"
            variant="outline"
            class="cursor-pointer transition-colors"
            :class="selected === category.slug ? 'bg-primary text-primary-foreground border-primary' : 'hover:bg-accent'"
            @click="toggle(category.slug, selected)"
        >
            {{ category.name }}
        </Badge>
    </div>
</template>
