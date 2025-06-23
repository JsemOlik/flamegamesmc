<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div
            class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-neutral-700 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-neutral-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M7 16h.01">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Celkem Serverů</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                        <span v-if="loading"
                            class="inline-block w-10 h-6 bg-neutral-200 dark:bg-neutral-700 rounded animate-pulse"></span>
                        <span v-else>{{ networkStats.totalServers }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Online Servery</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                        <span v-if="loading"
                            class="inline-block w-10 h-6 bg-neutral-200 dark:bg-neutral-700 rounded animate-pulse"></span>
                        <span v-else>{{ networkStats.onlineServers }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Online Hráči</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                        <span v-if="loading"
                            class="inline-block w-10 h-6 bg-neutral-200 dark:bg-neutral-700 rounded animate-pulse"></span>
                        <span v-else>{{ networkStats.totalPlayers }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Dnešní Peak</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                        <span v-if="loading"
                            class="inline-block w-10 h-6 bg-neutral-200 dark:bg-neutral-700 rounded animate-pulse"></span>
                        <span v-else>{{ networkStats.peakPlayers }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    networkStats: {
        totalServers: number;
        onlineServers: number;
        totalPlayers: number;
        peakPlayers: number;
    };
    loading?: boolean;
}>();
</script>

<style scoped></style>