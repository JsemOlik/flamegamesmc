<template>
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nedávné Logy</h3>
        </div>
        <div class="p-6">
            <div class="space-y-2">
                <div v-for="log in recentLogs" :key="log.time + log.message" class="flex items-start space-x-3">
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
}>();
</script>

<style scoped>
/* Add any specific styles for RecentLogs component here */
</style>