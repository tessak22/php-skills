<script setup lang="ts">
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1 space-y-2">
                    <!-- Title skeleton -->
                    <div class="h-5 w-3/4 animate-pulse rounded bg-muted" />
                    <!-- Category skeleton -->
                    <div class="h-3 w-1/3 animate-pulse rounded bg-muted" />
                </div>
            </div>
        </CardHeader>
        <CardContent class="flex-1 pb-3">
            <!-- Description skeleton -->
            <div class="space-y-2">
                <div class="h-4 w-full animate-pulse rounded bg-muted" />
                <div class="h-4 w-5/6 animate-pulse rounded bg-muted" />
            </div>
            <!-- Tags skeleton -->
            <div class="mt-3 flex gap-1">
                <div class="h-5 w-14 animate-pulse rounded-full bg-muted" />
                <div class="h-5 w-16 animate-pulse rounded-full bg-muted" />
                <div class="h-5 w-12 animate-pulse rounded-full bg-muted" />
            </div>
        </CardContent>
        <CardFooter class="border-t pt-3">
            <div class="flex w-full items-center justify-between">
                <div class="flex items-center gap-2">
                    <!-- Avatar skeleton -->
                    <div class="h-5 w-5 animate-pulse rounded-full bg-muted" />
                    <!-- Author name skeleton -->
                    <div class="h-3 w-20 animate-pulse rounded bg-muted" />
                </div>
                <!-- Install count skeleton -->
                <div class="h-3 w-10 animate-pulse rounded bg-muted" />
            </div>
        </CardFooter>
    </Card>
</template>
