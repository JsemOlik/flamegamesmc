<template>
    <div
        class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 mb-8">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Status Serverů</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Server</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Počet Hráčů</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            TPS</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Paměť</th>
                    </tr>
                </thead>
                <div v-if="loading" class="p-4 text-center w-full text-neutral-700 dark:text-neutral-300">
                    Načítání serverů...
                </div>
                <tbody v-else class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                    <tr v-for="node in nodes" :key="node.name" class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ node.name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-if="node.status === 'starting'"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Starting
                            </span>
                            <span v-else-if="node.online"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Online
                            </span>
                            <span v-else
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Offline
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-white">{{ node.players
                            }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-white">{{ node.tps }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-white">{{ node.ram }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const loading = ref(true)

const nodes = ref<Array<{
    name: string
    online: boolean
    players: number
    tps: number
    ram: string
    status: string
}>>([])

onMounted(async () => {
    loading.value = true
    try {
        const res = await axios.get('/api/servers/status')
        nodes.value = res.data
    } catch (error) {
        console.error('Failed to load server status:', error)
    } finally {
        loading.value = false
    }
})

</script>

<style scoped>
/* Optional styling */
</style>
