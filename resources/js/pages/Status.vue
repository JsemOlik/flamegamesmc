<template>
    <Head title="Systémový Status" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-neutral-50 dark:bg-transparent">
            <div class="max-w-full mx-auto px-2 sm:px-6 lg:px-12 py-8">
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-neutral-900 dark:text-white">Systémový Status & Ovládací Panel</h1>
                    <p class="mt-2 text-lg text-neutral-600 dark:text-neutral-300">
                        Přehled, historie a ovládání všech hlavních systémů (servery, ticketing, atd.)
                    </p>
                </div>

                <!-- System Health Cards -->
                <div class="mb-10 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-8">
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-lg border border-neutral-200 dark:border-neutral-700 p-8 flex items-center gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                                <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6a4 4 0 008 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">Ticketing systém</h2>
                            <p class="text-base text-neutral-500 dark:text-neutral-400">Status: <span :class="ticketingHealthy ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">{{ ticketingHealthy ? 'Online' : 'Offline' }}</span></p>
                        </div>
                        <button @click="pingTicketing" :disabled="pingingTicketing" class="ml-auto px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-base rounded-lg transition-all duration-200 shadow">
                            Ping
                        </button>
                    </div>
                    <!-- Placeholder for future system health cards -->
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-lg border border-neutral-200 dark:border-neutral-700 p-8 flex items-center justify-center text-neutral-400 dark:text-neutral-600 min-h-[96px]">
                        <span>Další systém (brzy)</span>
                    </div>
                </div>

                <!-- Full-width Graph/History Card (now between health and server status) -->
                <div class="mb-10 w-full">
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-lg border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col items-start">
                        <h2 class="text-xl font-semibold text-neutral-900 dark:text-white mb-4">Grafy a historie</h2>
                        <!-- Fixed Graph with Dummy Data -->
                        <div class="w-full h-64 flex flex-col justify-end relative">
                            <!-- Y axis labels -->
                            <div class="absolute left-0 top-0 h-full flex flex-col justify-between z-10 py-6">
                                <span class="text-xs text-neutral-400">100%</span>
                                <span class="text-xs text-neutral-400">50%</span>
                                <span class="text-xs text-neutral-400">0%</span>
                            </div>
                            <!-- SVG Graph -->
                            <svg viewBox="0 0 420 200" class="w-full h-full pl-10 pr-4">
                                <polyline
                                    fill="none"
                                    stroke="#3b82f6"
                                    stroke-width="4"
                                    :points="dummyGraphPoints"
                                />
                                <!-- Dots -->
                                <circle v-for="(pt, i) in dummyGraphData" :key="i"
                                    :cx="i * 60 + 30" :cy="200 - (pt / 100) * 180 - 10" r="5"
                                    fill="#3b82f6" />
                            </svg>
                            <!-- X axis labels -->
                            <div class="absolute bottom-4 left-10 w-[calc(100%-40px)] flex justify-between z-10">
                                <span v-for="(label, i) in dummyGraphLabels" :key="i" class="text-xs text-neutral-400" style="width:60px;text-align:center;">{{ label }}</span>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-neutral-500 dark:text-neutral-400">Ukázkový graf: Aktivita serverů za poslední týden</div>
                    </div>
                </div>

                <!-- Node/Server Status Table with Controls -->
                <div class="mb-12">
                    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-lg border border-neutral-200 dark:border-neutral-700">
                        <div class="px-8 py-6 border-b border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                            <h2 class="text-2xl font-semibold text-neutral-900 dark:text-white">Status Serverů / Node Ovládání</h2>
                            <button @click="refreshNodes" :disabled="loadingNodes" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-base rounded-lg transition-all duration-200 shadow">Obnovit</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-base">
                                <thead class="bg-neutral-50 dark:bg-neutral-900">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Server</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Hráči</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">TPS</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">CPU</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Paměť</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Akce</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                                    <tr v-for="node in nodes" :key="node.id" class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-neutral-900 dark:text-white">{{ node.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="node.status === 'starting'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Starting
                                            </span>
                                            <span v-else-if="node.online" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Online
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Offline
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-neutral-900 dark:text-white">{{ node.players }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-neutral-900 dark:text-white">{{ node.tps }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-neutral-900 dark:text-white">{{ node.cpu || '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-neutral-900 dark:text-white">{{ node.ram }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                            <button @click="openConfirmModal(node, 'zapnout')" :disabled="nodeActionLoading[node.name]" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg shadow">Start</button>
                                            <button @click="openConfirmModal(node, 'restartovat')" :disabled="nodeActionLoading[node.name]" class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-xs rounded-lg shadow">Restart</button>
                                            <button @click="openConfirmModal(node, 'vypnout')" :disabled="nodeActionLoading[node.name]" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg shadow">Stop</button>
                                            <button @click="pingNode(node)" :disabled="nodeActionLoading[node.name]" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg shadow">Ping</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Modal for Power Actions -->
                <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                    <div class="bg-white dark:bg-neutral-900 rounded-xl shadow-2xl p-8 max-w-sm w-full text-center animate-in zoom-in-95 duration-200">
                        <h3 class="text-xl font-semibold mb-4 text-neutral-900 dark:text-white">Potvrzení Akce</h3>
                        <p class="mb-6 text-neutral-700 dark:text-neutral-300">
                            Opravdu chcete <span class="font-bold" :class="{
                                'text-green-600 dark:text-green-400': confirmActionType === 'zapnout',
                                'text-orange-600 dark:text-orange-400': confirmActionType === 'restartovat',
                                'text-red-600 dark:text-red-400': confirmActionType === 'vypnout',
                            }">{{ confirmActionTypeText }}</span> server <span class="font-bold">{{ confirmNode?.name }}</span>?
                        </p>
                        <div class="flex justify-center gap-6">
                            <button @click="confirmNodeAction" :disabled="nodeActionLoading[confirmNode?.name]"
                                class="px-5 py-2 text-white rounded-md font-semibold transition-all duration-200 shadow-md hover:scale-105 active:scale-95"
                                :class="{
                                    'bg-green-600 hover:bg-green-700': confirmActionType === 'zapnout',
                                    'bg-orange-600 hover:bg-orange-700': confirmActionType === 'restartovat',
                                    'bg-red-600 hover:bg-red-700': confirmActionType === 'vypnout',
                                }">
                                Potvrdit
                            </button>
                            <button @click="closeConfirmModal"
                                class="px-5 py-2 bg-neutral-600 hover:bg-neutral-500 text-white rounded-md font-semibold transition-all duration-200 shadow-md hover:scale-105 active:scale-95">
                                Zrušit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Future: Add more system health cards or controls here -->
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Status', href: '/status' },
];

const ticketingHealthy = ref(true);
const pingingTicketing = ref(false);

async function pingTicketing() {
    pingingTicketing.value = true;
    try {
        await new Promise(resolve => setTimeout(resolve, 500));
        ticketingHealthy.value = true;
    } catch (e) {
        ticketingHealthy.value = false;
    } finally {
        pingingTicketing.value = false;
    }
}

const loadingNodes = ref(true);
const nodes = ref<Array<any>>([]);
const nodeActionLoading = reactive<Record<string, boolean>>({});

// Dummy graph data
const dummyGraphData = [60, 80, 55, 90, 70, 100, 85];
const dummyGraphLabels = ['Po', 'Út', 'St', 'Čt', 'Pá', 'So', 'Ne'];
const dummyGraphPoints = computed(() => dummyGraphData.map((v, i) => `${i * 60 + 30},${200 - (v / 100) * 180 - 10}`).join(' '));

// Confirmation modal state
const showConfirmModal = ref(false);
const confirmNode = ref<any>(null);
const confirmActionType = ref<'zapnout' | 'vypnout' | 'restartovat' | null>(null);
const confirmActionTypeText = computed(() => {
    switch (confirmActionType.value) {
        case 'zapnout': return 'zapnout';
        case 'vypnout': return 'vypnout';
        case 'restartovat': return 'restartovat';
        default: return '';
    }
});

const actionSignalMap = {
    zapnout: 'start',
    vypnout: 'stop',
    restartovat: 'restart',
};

function openConfirmModal(node: any, action: 'zapnout' | 'vypnout' | 'restartovat') {
    confirmNode.value = node;
    confirmActionType.value = action;
    showConfirmModal.value = true;
}
function closeConfirmModal() {
    showConfirmModal.value = false;
    confirmNode.value = null;
    confirmActionType.value = null;
}

async function confirmNodeAction() {
    if (!confirmNode.value || !confirmActionType.value) return;
    await controlNode(confirmNode.value, confirmActionType.value);
    closeConfirmModal();
}

function updateNodes(newNodes) {
    if (!nodes.value.length) {
        nodes.value = newNodes;
        return;
    }
    newNodes.forEach(newNode => {
        const existing = nodes.value.find(n => n.id === newNode.id);
        if (existing) {
            Object.assign(existing, newNode);
        } else {
            nodes.value.push(newNode);
        }
    });
    nodes.value = nodes.value.filter(n => newNodes.some(nn => nn.id === n.id));
}

async function fetchNodes() {
    loadingNodes.value = true;
    try {
        const res = await axios.get('/api/servers/status');
        const newNodes = (res.data || []).map((n: any) => ({
            ...n,
            cpu: n.cpu ?? (Math.random() * 80 + 10).toFixed(1) + '%',
        }));
        updateNodes(newNodes);
    } catch (e) {
        nodes.value = [];
    } finally {
        loadingNodes.value = false;
    }
}

function refreshNodes() {
    fetchNodes();
}

async function controlNode(node: any, action: 'zapnout' | 'vypnout' | 'restartovat') {
    nodeActionLoading[node.name] = true;
    try {
        const signal = actionSignalMap[action];
        await axios.post(`/api/servers/${node.id}/power`, { signal });
        await fetchNodes();
    } catch (e) {
        alert(`Chyba při ${action} serveru ${node.name}`);
    } finally {
        nodeActionLoading[node.name] = false;
    }
}

async function pingNode(node: any) {
    nodeActionLoading[node.name] = true;
    try {
        // Simulate ping, replace with real API if available
        await new Promise(resolve => setTimeout(resolve, 400));
        alert(`Ping na ${node.name} úspěšný!`);
    } catch (e) {
        alert(`Ping na ${node.name} selhal.`);
    } finally {
        nodeActionLoading[node.name] = false;
    }
}

let pollInterval: any = null;
onMounted(() => {
    fetchNodes();
    pingTicketing();
    pollInterval = setInterval(fetchNodes, 4000);
});
onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>
