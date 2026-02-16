<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
</script>

<template>
    <Card>
        <CardContent class="p-4">
            <div class="flex gap-3">
                <!-- Avatar skeleton -->
                <div class="h-10 w-10 shrink-0 animate-pulse rounded-full bg-muted" />
                <div class="min-w-0 flex-1">
                    <!-- Author row skeleton -->
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-24 animate-pulse rounded bg-muted" />
                        <div class="h-3 w-20 animate-pulse rounded bg-muted" />
                        <div class="ml-auto h-5 w-14 animate-pulse rounded-full bg-muted" />
                    </div>
                    <!-- Content skeleton -->
                    <div class="mt-2 space-y-2">
                        <div class="h-4 w-full animate-pulse rounded bg-muted" />
                        <div class="h-4 w-full animate-pulse rounded bg-muted" />
                        <div class="h-4 w-2/3 animate-pulse rounded bg-muted" />
                    </div>
                    <!-- Footer skeleton -->
                    <div class="mt-3 flex items-center justify-between">
                        <div class="h-3 w-16 animate-pulse rounded bg-muted" />
                        <div class="h-3 w-20 animate-pulse rounded bg-muted" />
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
