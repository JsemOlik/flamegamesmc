<template>

    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-neutral-50 dark:bg-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Server Dashboard</h1>
                    <p class="mt-2 text-neutral-600 dark:text-neutral-300">
                        Centrální monitorování a ovládání FlameGames Minecraft sítě
                    </p>
                </div>

                <NetworkStats :networkStats="networkStats" />

                <div v-if="loadingNodes && nodes.length === 0">
                    <Skeleton class="h-32 w-full mb-8" />
                </div>
                <NodeStatusTable v-else :nodes="nodes" />

                <QuickActions />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <OpenTickets :openTickets="openTickets" :loading="loadingTickets" />
                    <AdminActions :adminActions="adminActions" :loading="loadingAdminActions" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import NetworkStats from '@/components/dashboard/NetworkStats.vue';
import NodeStatusTable from '@/components/dashboard/NodeStatusTable.vue';
import OpenTickets from '@/components/dashboard/OpenTickets.vue';
import QuickActions from '@/components/dashboard/QuickActions.vue';
import AdminActions from '@/components/dashboard/AdminActions.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const networkStats = ref({
    totalServers: 0,
    onlineServers: 0,
    totalPlayers: 0,
    peakPlayers: 0,
});

const nodes = ref<Array<any>>([]);
const loadingNodes = ref(true);
let firstLoad = true;

let pollInterval: any = null;
onMounted(async () => {
    loadingNodes.value = true;
    await fetchStatus();
    loadingNodes.value = false;
    firstLoad = false;
    pollInterval = setInterval(fetchStatus, 10000);
});
onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});

async function fetchStatus() {
    try {
        const res = await axios.get('/api/servers/status');
        const servers = res.data;
        networkStats.value.totalServers = servers.length;
        networkStats.value.onlineServers = servers.filter(s => s.online).length;
        // Update nodes in-place for reactivity
        if (!nodes.value.length) {
            nodes.value = servers;
        } else {
            servers.forEach(newNode => {
                const existing = nodes.value.find(n => n.id === newNode.id);
                if (existing) {
                    Object.assign(existing, newNode);
                } else {
                    nodes.value.push(newNode);
                }
            });
            // Remove nodes that no longer exist
            nodes.value = nodes.value.filter(n => servers.some(nn => nn.id === n.id));
        }
    } catch (e) {
        // handle error
    }
}

const openTickets = ref([]);
const loadingTickets = ref(true);
const adminActions = ref([]);
const loadingAdminActions = ref(true);

onMounted(async () => {
    await fetchRecentTickets();
    await fetchAdminActions();
});

async function fetchRecentTickets() {
    loadingTickets.value = true;
    try {
        const res = await axios.get('/tickets/recent');
        openTickets.value = res.data;
    } catch (e) {
        openTickets.value = [];
    }
    loadingTickets.value = false;
}


async function fetchAdminActions() {
    loadingAdminActions.value = true;
    try {
        const res = await axios.get('/api/recent-logs');
        // Map to the format expected by AdminActions.vue
        adminActions.value = (res.data || []).map(log => {
            return {
                admin: log.user,
                action: log.message,
                time: log.time,
                role: log.role,
            };
        });
    } catch (e) {
        adminActions.value = [];
    }
    loadingAdminActions.value = false;
}

</script>