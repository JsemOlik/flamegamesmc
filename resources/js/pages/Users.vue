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
    { id: 2, subject: 'Can’t connect to server', status: 'Open', created: '2025-06-21', user: 'Alex' },
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
        <div class="flex flex-col gap-8 p-4 min-h-screen">
            <!-- Network Overview -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-xl border-2 border-orange-500 p-4 shadow-lg flex flex-col items-center">
                    <span class="text-3xl font-bold text-orange-500">{{ networkStats.totalServers }}</span>
                    <span class="uppercase text-xs tracking-widest text-orange-500">Total Servers</span>
                </div>
                <div class="rounded-xl border-2 border-orange-500 p-4 shadow-lg flex flex-col items-center">
                    <span class="text-3xl font-bold text-orange-500">{{ networkStats.onlineServers }}</span>
                    <span class="uppercase text-xs tracking-widest text-orange-500">Online Servers</span>
                </div>
                <div class="rounded-xl border-2 border-orange-500 p-4 shadow-lg flex flex-col items-center">
                    <span class="text-3xl font-bold text-orange-500">{{ networkStats.totalPlayers }}</span>
                    <span class="uppercase text-xs tracking-widest text-orange-500">Players Online</span>
                </div>
                <div class="rounded-xl border-2 border-orange-500 p-4 shadow-lg flex flex-col items-center">
                    <span class="text-3xl font-bold text-orange-500">{{ networkStats.peakPlayers }}</span>
                    <span class="uppercase text-xs tracking-widest text-orange-500">Peak Today</span>
                </div>
            </div>

            <!-- Nodes Status -->
            <div>
                <h2 class="font-bold text-xl mb-2 text-orange-500">Node Status</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-orange-200 rounded-xl">
                        <thead>
                            <tr class="bg-orange-100 text-orange-700">
                                <th class="p-2">Node</th>
                                <th class="p-2">Status</th>
                                <th class="p-2">Players</th>
                                <th class="p-2">TPS</th>
                                <th class="p-2">RAM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="node in nodes" :key="node.name" :class="node.online ? '' : 'bg-orange-50'">
                                <td class="p-2 font-semibold">{{ node.name }}</td>
                                <td class="p-2">
                                    <span :class="node.online ? 'text-green-600 font-bold' : 'text-red-500 font-bold'">
                                        ● {{ node.online ? 'Online' : 'Offline' }}
                                    </span>
                                </td>
                                <td class="p-2">{{ node.players }}</td>
                                <td class="p-2">{{ node.tps }}</td>
                                <td class="p-2">{{ node.ram }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div>
                <h2 class="font-bold text-xl mb-2 text-orange-500">Quick Actions</h2>
                <div class="flex gap-4 flex-wrap">
                    <button
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded shadow transition">
                        Restart All Servers
                    </button>
                    <button
                        class="border border-orange-500 hover:bg-orange-100 text-orange-600 font-bold py-2 px-4 rounded shadow transition">
                        Broadcast Message
                    </button>
                    <button
                        class="border border-orange-500 hover:bg-orange-100 text-orange-600 font-bold py-2 px-4 rounded shadow transition">
                        View Backups
                    </button>
                </div>
            </div>

            <!-- Main Cards -->
            <div class="grid md:grid-cols-3 gap-4">
                <!-- Open Tickets -->
                <div class="rounded-xl border-2 border-orange-500 p-4">
                    <h2 class="font-bold text-lg mb-2 text-orange-500">Open Tickets</h2>
                    <ul>
                        <li v-for="ticket in openTickets" :key="ticket.id" class="mb-3">
                            <div class="font-semibold">{{ ticket.subject }}</div>
                            <div class="text-xs text-gray-500">
                                #{{ ticket.id }} • {{ ticket.user }} • {{ ticket.created }}
                            </div>
                            <span class="inline-block px-2 py-0.5 rounded bg-orange-100 text-orange-700 text-xs">{{
                                ticket.status }}</span>
                        </li>
                    </ul>
                </div>
                <!-- Recent Player Activity -->
                <div class="rounded-xl border-2 border-orange-500 p-4">
                    <h2 class="font-bold text-lg mb-2 text-orange-500">Recent Player Activity</h2>
                    <ul>
                        <li v-for="p in recentPlayers" :key="p.name + p.time" class="mb-2">
                            <span class="font-semibold text-orange-700">{{ p.name }}</span>
                            <span class="text-xs text-orange-500"> {{ p.action }} </span>
                            <span class="text-gray-700">on {{ p.server }}</span>
                            <span class="text-xs text-gray-400 float-right">{{ p.time }}</span>
                        </li>
                    </ul>
                </div>
                <!-- Recent Logs -->
                <div class="rounded-xl border-2 border-orange-500 p-4">
                    <h2 class="font-bold text-lg mb-2 text-orange-500">Recent Logs</h2>
                    <ul class="text-xs font-mono text-gray-700">
                        <li v-for="log in recentLogs" :key="log.time + log.message">
                            <span class="text-orange-400">{{ log.time }}</span> {{ log.message }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Admin Actions -->
            <div>
                <h2 class="font-bold text-xl mb-2 text-orange-500">Recent Admin Actions</h2>
                <ul>
                    <li v-for="a in adminActions" :key="a.admin + a.time" class="mb-2">
                        <span class="font-semibold text-orange-700">{{ a.admin }}</span>
                        <span class="text-gray-700"> {{ a.action }} </span>
                        <span class="text-xs text-gray-400 float-right">{{ a.time }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
