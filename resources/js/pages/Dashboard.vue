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

                <NetworkStats :networkStats="networkStats" :loading="loading" />

                <NodeStatusTable :nodes="nodes" />

                <QuickActions />

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <OpenTickets :openTickets="openTickets" />
                    <PlayerActivity :recentPlayers="recentPlayers" />
                    <RecentLogs :recentLogs="recentLogs" />
                </div>

                <AdminActions :adminActions="adminActions" />
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
import PlayerActivity from '@/components/dashboard/PlayerActivity.vue';
import QuickActions from '@/components/dashboard/QuickActions.vue';
import RecentLogs from '@/components/dashboard/RecentLogs.vue';
import AdminActions from '@/components/dashboard/AdminActions.vue';
import { ref, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const loading = ref(true);

const networkStats = ref({
    totalServers: 0,
    onlineServers: 0,
    totalPlayers: 0,
    peakPlayers: 0,
});

onMounted(async () => {
    loading.value = true;
    try {
        const res = await fetch('/api/servers/status');
        const servers = await res.json();
        networkStats.value.totalServers = servers.length;
        networkStats.value.onlineServers = servers.filter(s => s.online).length;
        // If you have player/peak data, set it here as well
    } catch (e) {
        // handle error
    } finally {
        loading.value = false;
    }
});

const nodes = [
    { name: 'Proxy-1', online: true, players: 32, tps: 19.8, ram: '2.1GB/4GB' },
    { name: 'Lobby-1', online: true, players: 15, tps: 20.0, ram: '1.2GB/2GB' },
    { name: 'Survival-1', online: true, players: 28, tps: 19.7, ram: '3.5GB/8GB' },
    { name: 'Minigames-1', online: false, players: 0, tps: 0, ram: '0GB/4GB' },
    { name: 'Skyblock-1', online: true, players: 12, tps: 19.9, ram: '2.8GB/4GB' },
];

const openTickets = [
    { id: 1, subject: 'Griefing in spawn', status: 'Open', created: '2025-06-22', user: 'Steve' },
    { id: 2, subject: 'Cannot connect to server', status: 'Open', created: '2025-06-21', user: 'Alex' },
    { id: 3, subject: 'Lag on Skyblock', status: 'Open', created: '2025-06-20', user: 'Herobrine' },
];

const recentLogs = [
    { time: '12:01', message: '[INFO] Player Steve joined Survival-1' },
    { time: '12:03', message: '[WARN] Lag detected on Skyblock-1' },
    { time: '12:05', message: '[INFO] Player Alex left Lobby-1' },
    { time: '12:06', message: '[ADMIN] /restart Minigames-1 by Admin' },
];

const recentPlayers = [
    { name: 'Steve', action: 'joined', server: 'Survival-1', time: '12:01' },
    { name: 'Alex', action: 'left', server: 'Lobby-1', time: '12:05' },
    { name: 'Herobrine', action: 'joined', server: 'Skyblock-1', time: '12:10' },
];

const adminActions = [
    { admin: 'Admin', action: 'Restarted Minigames-1', time: '12:06' },
    { admin: 'Mod', action: 'Banned Notch', time: '11:50' },
];
</script>