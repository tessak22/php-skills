<script setup lang="ts">
import { Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';

type Props = {
    modelValue?: string;
    placeholder?: string;
};

type Emits = {
    'update:modelValue': [value: string];
    search: [value: string];
};

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    placeholder: 'Search skills...',
});

const emit = defineEmits<Emits>();
const query = ref(props.modelValue);

let debounceTimer: ReturnType<typeof setTimeout>;

watch(query, (value) => {
    emit('update:modelValue', value);
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        emit('search', value);
    }, 300);
});

watch(
    () => props.modelValue,
    (value) => {
        query.value = value;
    },
);

function clear() {
    query.value = '';
    emit('update:modelValue', '');
    emit('search', '');
}
</script>

<template>
    <div class="relative">
        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        <Input
            v-model="query"
            type="search"
            :placeholder="placeholder"
            class="pl-9 pr-9"
        />
        <button
            v-if="query"
            type="button"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
            @click="clear"
        >
            <X class="h-4 w-4" />
        </button>
    </div>
</template>
