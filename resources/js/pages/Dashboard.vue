<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

// Dummy data
const networkStats = {
    totalServers: 5,
    onlineServers: 4,
    totalPlayers: 87,
    peakPlayers: 120,
};

const nodes = [
    { name: 'Proxy-1', online: true, players: 32, tps: 19.8, ram: '2.1GB/4GB' },
    { name: 'Lobby-1', online: true, players: 15, tps: 20.0, ram: '1.2GB/2GB' },
    { name: 'Survival-1', online: true, players: 28, tps: 19.7, ram: '3.5GB/8GB' },
    { name: 'Minigames-1', online: false, players: 0, tps: 0, ram: '0GB/4GB' },
    { name: 'Skyblock-1', online: true, players: 12, tps: 19.9, ram: '2.8GB/4GB' },
];

const openTickets = [
    { id: 1, subject: 'Griefing in spawn', status: 'Open', created: '2025-06-22', user: 'Steve' },
    { id: 2, subject: 'Cannot connect to server', status: 'Open', created: '2025-06 - 21', user: 'Alex' },
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

<template>

    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-neutral-50 dark:bg-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Welcome Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">
                        Server Dashboard
                    </h1>
                    <p class="mt-2 text-neutral-600 dark:text-neutral-300">
                        <!-- Monitor and manage your Minecraft network -->
                        Centrální monitorování a ovládání FlameGames Minecraft sítě
                    </p>
                </div>

                <!-- Network Overview Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div
                        class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-blue-100 dark:bg-neutral-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-neutral-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M7 16h.01">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Celkem Serverů</p>
                                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{
                                    networkStats.totalServers
                                }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Online Servery</p>
                                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{
                                    networkStats.onlineServers }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Online Hráči</p>
                                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{
                                    networkStats.totalPlayers }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Dnešní Peak</p>
                                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{
                                    networkStats.peakPlayers
                                }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Server Status Table -->
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
                            <tbody
                                class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                                <tr v-for="node in nodes" :key="node.name"
                                    class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ node.name
                                        }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="node.online"
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-white">{{
                                        node.players }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-white">{{
                                        node.tps }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-white">{{
                                        node.ram }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Rychlé Akce</h2>
                    <div class="flex flex-wrap gap-3">
                        <button
                            class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Restartovat Všechny Servery
                        </button>
                        <button
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-900 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm font-medium rounded-lg border border-neutral-300 dark:border-neutral-600 shadow-sm transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                </path>
                            </svg>
                            Ohlásit Zprávu
                        </button>
                        <button
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-900 hover:bg-neutral-50 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm font-medium rounded-lg border border-neutral-300 dark:border-neutral-600 shadow-sm transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                </path>
                            </svg>
                            Zobrazit Zálohy
                        </button>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Open Tickets -->
                    <div
                        class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Otevřené Tickety</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="ticket in openTickets" :key="ticket.id"
                                    class="border-l-4 border-orange-500 bg-orange-50 dark:bg-orange-900/20 p-4 rounded-r-lg">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-neutral-900 dark:text-white">{{
                                            ticket.subject
                                        }}</h4>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
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

                    <!-- Recent Player Activity -->
                    <div
                        class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nedávná Aktivita</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div v-for="player in recentPlayers" :key="player.name + player.time"
                                    class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-8 h-8 bg-neutral-100 dark:bg-neutral-700 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-neutral-900 dark:text-white">{{
                                                player.name
                                            }}</p>
                                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ player.action
                                            }} {{
                                                    player.server }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ player.time
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Logs -->
                    <div
                        class="bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nedávné Logy</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2">
                                <div v-for="log in recentLogs" :key="log.time + log.message"
                                    class="flex items-start space-x-3">
                                    <span
                                        class="text-xs font-mono text-neutral-500 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2 py-1 rounded">{{
                                            log.time }}</span>
                                    <p class="text-xs font-mono text-neutral-700 dark:text-neutral-300 flex-1">{{
                                        log.message
                                    }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Admin Actions -->
                <div
                    class="mt-8 bg-white dark:bg-neutral-900 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-700">
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nedávné Admin Akce</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div v-for="action in adminActions" :key="action.admin + action.time"
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
                                        <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ action.admin
                                        }}
                                        </p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ action.action }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ action.time }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>