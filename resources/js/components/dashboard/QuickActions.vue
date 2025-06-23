<script setup lang="ts">
import axios from 'axios'
import { ref } from 'vue'
import { Power, RotateCcw } from 'lucide-vue-next';


const serverIds = ['fe89fc90', '3db54e5e', '0aef5bde', '2027770a', '271c6921']

const isProcessing = ref(false)
const showModal = ref(false)
const actionToConfirm = ref<'zapnout' | 'vypnout' | 'restartovat' | null>(null)

function openConfirmModal(action: 'zapnout' | 'vypnout' | 'restartovat') {
    actionToConfirm.value = action
    showModal.value = true
}

async function confirmAction() {
    if (!actionToConfirm.value) return

    isProcessing.value = true
    showModal.value = false
    try {
        const promises = serverIds.map(id =>
            axios.post(`/api/servers/${id}/${actionToConfirm.value}`)
        )
        await Promise.all(promises)
        alert(`Servers ${actionToConfirm.value}ed successfully!`)
    } catch (e) {
        alert(`Failed to ${actionToConfirm.value} servers`)
        console.error(e)
    } finally {
        isProcessing.value = false
        actionToConfirm.value = null
    }
}

function cancelAction() {
    showModal.value = false
    actionToConfirm.value = null
}
</script>

<template>
    <div class="mb-8 relative">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Rychlé Akce</h2>
        <div class="flex flex-wrap gap-3">
            <button :disabled="isProcessing" @click="openConfirmModal('zapnout')"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                Zapnout Servery
            </button>

            <button :disabled="isProcessing" @click="openConfirmModal('restartovat')"
                class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                <RotateCcw class="mr-1" />Restartovat Servery
            </button>

            <button :disabled="isProcessing" @click="openConfirmModal('vypnout')"
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                <Power class="mr-1" />
                Vypnout Servery
            </button>

            <button
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-900 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm font-medium rounded-lg border border-neutral-300 dark:border-neutral-600 shadow-sm transition-colors duration-200">
                Ohlásit Zprávu
            </button>
            <button
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-900 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm font-medium rounded-lg border border-neutral-300 dark:border-neutral-600 shadow-sm transition-colors duration-200">
                Zobrazit Zálohy
            </button>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="showModal"
            class="fixed inset-0 backdrop-blur-sm bg-white/30 dark:bg-neutral-900/30 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-lg max-w-sm w-full p-6 text-center" @click.stop>
                <h3 class="text-lg font-semibold mb-4 text-neutral-900 dark:text-white">Potvrzení Akce</h3>
                <p class="mb-6 text-neutral-700 dark:text-neutral-300">
                    Opravdu chcete
                    <span class="font-bold text-orange-600 dark:text-orange-400">{{ actionToConfirm }}</span>
                    všechny servery?
                </p>
                <div class="flex justify-center gap-6">
                    <button @click="confirmAction"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-semibold transition">
                        Potvrdit
                    </button>
                    <button @click="cancelAction"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-500 rounded-md font-semibold transition">
                        Zrušit
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
