<script setup lang="ts">
import { Check, Copy } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';

type Props = {
    text: string;
    label?: string;
};

const props = withDefaults(defineProps<Props>(), {
    label: 'Copy install command',
});

const copied = ref(false);

async function copy() {
    try {
        await navigator.clipboard.writeText(props.text);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    } catch {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = props.text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    }
}
</script>

<template>
    <div class="flex items-center gap-2">
        <code class="flex-1 rounded-md bg-muted px-3 py-2 text-sm font-mono text-foreground">
            {{ text }}
        </code>
        <Button
            variant="outline"
            size="icon"
            class="shrink-0"
            :aria-label="label"
            @click="copy"
        >
            <Check v-if="copied" class="h-4 w-4 text-green-500" />
            <Copy v-else class="h-4 w-4" />
        </Button>
    </div>
</template>
