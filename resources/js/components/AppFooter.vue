<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <footer class="border-t border-border bg-muted/30">
        <div class="mx-auto max-w-7xl px-4 py-8">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <Link href="/" class="flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors">
                    <AppLogoIcon class="h-5 w-5 fill-current" />
                    <span class="font-semibold">LaravelSkills</span>
                </Link>
                <nav class="flex gap-6 text-sm text-muted-foreground">
                    <Link href="/skills" class="hover:text-foreground transition-colors">Skills</Link>
                    <Link href="/feed" class="hover:text-foreground transition-colors">Feed</Link>
                    <Link href="/docs" class="hover:text-foreground transition-colors">Docs</Link>
                </nav>
                <p class="text-sm text-muted-foreground">
                    Built with Laravel. Powered by the community.
                </p>
            </div>
        </div>
    </footer>
</template>
