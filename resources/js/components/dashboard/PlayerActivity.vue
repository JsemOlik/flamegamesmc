<template>
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nedávná Aktivita</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <div v-for="player in recentPlayers" :key="player.name + player.time" class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-neutral-100 dark:bg-neutral-700 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ player.name }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ player.action }} {{ player.server }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ player.time }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{
    recentPlayers: Array<{
        name: string;
        action: string;
        server: string;
        time: string;
    }>;
}>();
</script>

<style scoped>
/* Add any specific styles for PlayerActivity component here */
</style>