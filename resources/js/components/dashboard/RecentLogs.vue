<template>
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nedávné Logy</h3>
        </div>
        <div class="p-6">
            <div v-if="props.loading" class="space-y-2 animate-pulse">
                <div class="h-4 bg-neutral-200 dark:bg-neutral-700 rounded w-2/3 mb-2"></div>
                <div class="h-4 bg-neutral-200 dark:bg-neutral-700 rounded w-1/2 mb-2"></div>
                <div class="h-4 bg-neutral-200 dark:bg-neutral-700 rounded w-3/4"></div>
            </div>
            <div v-else-if="props.recentLogs.length === 0" class="text-center text-neutral-500 dark:text-neutral-400 py-4">
                Žádné logy k zobrazení.
            </div>
            <div v-else class="space-y-2">
                <div v-for="log in props.recentLogs" :key="log.time + log.message" class="flex items-start space-x-3">
                    <span class="text-xs font-mono text-neutral-500 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2 py-1 rounded">{{ log.time }}</span>
                    <p class="text-xs font-mono text-neutral-700 dark:text-neutral-300 flex-1">{{ log.message }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{
    recentLogs: Array<{ time: string; message: string }>;
    loading?: boolean;
}>();
</script>