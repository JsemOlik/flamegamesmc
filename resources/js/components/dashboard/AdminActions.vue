<template>
    <div
        class="mt-8 bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nedávné Admin Akce</h3>
        </div>
        <div class="p-6">
            <div v-if="props.loading" class="space-y-3 animate-pulse">
                <div class="h-6 bg-orange-200 dark:bg-orange-900/40 rounded w-3/4 mb-2"></div>
                <div class="h-6 bg-orange-200 dark:bg-orange-900/40 rounded w-2/3 mb-2"></div>
                <div class="h-6 bg-orange-200 dark:bg-orange-900/40 rounded w-1/2"></div>
            </div>
            <div v-else-if="props.adminActions.length === 0" class="text-center text-neutral-500 dark:text-neutral-400 py-4">
                Žádné admin akce k zobrazení.
            </div>
            <div v-else class="space-y-3">
                <div v-for="action in props.adminActions" :key="action.admin + action.time"
                    class="flex items-center justify-between p-3 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ action.admin }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ action.action }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ action.time }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{
    adminActions: Array<{
        admin: string;
        action: string;
        time: string;
    }>;
    loading?: boolean;
}>();
</script>

<style scoped></style>