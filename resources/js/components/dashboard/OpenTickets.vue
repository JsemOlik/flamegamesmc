<template>
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Otevřené Tickety</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div v-for="ticket in openTickets" :key="ticket.id" class="border-l-4 border-orange-500 bg-orange-50 dark:bg-orange-900/20 p-4 rounded-r-lg">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-neutral-900 dark:text-white">{{ ticket.subject }}</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            {{ ticket.status }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                        #{{ ticket.id }} • {{ ticket.user }} • {{ ticket.created }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    openTickets: Array<{
        id: number;
        subject: string;
        status: string;
        created: string;
        user: string;
    }>
}>();
</script>

<style scoped>
/* Add any specific styles for OpenTickets component here */
</style>