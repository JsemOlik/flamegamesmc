<template>

    <Head title="Detail Ticketu" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-neutral-50 dark:bg-transparent">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header -->
                <div class="mb-8 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">
                                Ticket #{{ ticket.id }}
                            </h1>
                            <span :class="getStatusClass(ticket.status)"
                                class="px-3 py-1 text-sm font-medium rounded-full">
                                {{ getStatusText(ticket.status) }}
                            </span>
                            <span :class="getPriorityClass(ticket.priority)"
                                class="px-3 py-1 text-sm font-medium rounded-full">
                                {{ getPriorityText(ticket.priority) }}
                            </span>
                        </div>
                        <h2 class="text-xl text-neutral-600 dark:text-neutral-300 mb-2">{{ ticket.subject }}</h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            Vytvořil {{ ticket.user }} • {{ ticket.created }} • {{ getCategoryText(ticket.category) }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <select v-model="ticket.status" @change="updateStatus"
                            class="rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white px-3 py-2">
                            <option value="open">Otevřené</option>
                            <option value="in_progress">V řešení</option>
                            <option value="resolved">Vyřešené</option>
                            <option value="closed">Uzavřené</option>
                        </select>
                        <button @click="$router.go(-1)"
                            class="px-4 py-2 border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700">
                            Zpět
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Messages -->
                    <div class="lg:col-span-2">
                        <div
                            class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Konverzace</h3>
                            </div>

                            <div class="p-6 space-y-6 max-h-96 overflow-y-auto" ref="messagesContainer">
                                <div v-for="message in messages" :key="message.id" class="flex gap-4"
                                    :class="message.isAdmin ? 'flex-row-reverse' : ''">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        <div v-if="message.isAdmin"
                                            class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <img v-else :src="getAvatarUrl(message)" alt="Player avatar"
                                            class="w-10 h-10 rounded-lg bg-green-600 object-cover" loading="lazy" />
                                    </div>

                                    <!-- Message Content -->
                                    <div :class="message.isAdmin ? 'text-right' : 'text-left'"
                                        class="flex-1 max-w-xs sm:max-w-md">
                                        <div :class="message.isAdmin ? 'bg-blue-600 text-white ml-auto' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-900 dark:text-white'"
                                            class="rounded-2xl px-4 py-3 inline-block">
                                            <p class="text-sm whitespace-pre-wrap">{{ message.content }}</p>
                                        </div>
                                        <div class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                            <span
                                                :class="message.isAdmin ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-green-600 dark:text-green-400 font-medium'">
                                                {{ message.isAdmin ? 'Admin' : message.author }}
                                            </span>
                                            <span class="mx-1">•</span>
                                            <span>{{ message.timestamp }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reply Form -->
                            <div class="border-t border-neutral-200 dark:border-neutral-700 p-6">
                                <form @submit.prevent="sendReply" class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                            Odpověď
                                        </label>
                                        <textarea v-model="replyText" rows="4" placeholder="Napište svou odpověď..."
                                            class="w-full rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required></textarea>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center">
                                                <input type="checkbox" v-model="markAsResolved"
                                                    class="rounded border-neutral-300 dark:border-neutral-600 text-blue-600 focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">
                                                    Označit jako vyřešené
                                                </span>
                                            </label>

                                            <label class="flex items-center">
                                                <input type="checkbox" v-model="notifyUser"
                                                    class="rounded border-neutral-300 dark:border-neutral-600 text-blue-600 focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">
                                                    Upozornit uživatele
                                                </span>
                                            </label>
                                        </div>

                                        <button type="submit" :disabled="!replyText.trim()"
                                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-neutral-400 text-white rounded-lg font-medium transition-colors">
                                            Odeslat odpověď
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Ticket Info -->
                        <div
                            class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Informace o ticketu
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Priorita</label>
                                    <select v-model="ticket.priority" @change="updatePriority"
                                        class="w-full rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white px-3 py-2">
                                        <option value="low">Nízká</option>
                                        <option value="medium">Střední</option>
                                        <option value="high">Vysoká</option>
                                        <option value="urgent">Urgentní</option>
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Kategorie</label>
                                    <select v-model="ticket.category" @change="updateCategory"
                                        class="w-full rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white px-3 py-2">
                                        <option value="technical">Technické</option>
                                        <option value="gameplay">Gameplay</option>
                                        <option value="player_report">Nahlášení hráče</option>
                                        <option value="other">Ostatní</option>
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Přiřazeno</label>
                                    <select v-model="ticket.assignedTo" @change="updateAssignee"
                                        class="w-full rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white px-3 py-2">
                                        <option value="">Nepřiřazeno</option>
                                        <option value="admin1">Admin</option>
                                        <option value="mod1">Moderátor</option>
                                        <option value="support1">Support</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div
                            class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Informace o
                                uživateli</h3>

                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-900 dark:text-white">{{ ticket.user }}</p>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Hráč</p>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-neutral-600 dark:text-neutral-300">Registrován:</span>
                                        <span class="text-neutral-900 dark:text-white">2024-01-15</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-neutral-600 dark:text-neutral-300">Poslední aktivita:</span>
                                        <span class="text-neutral-900 dark:text-white">Dnes</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-neutral-600 dark:text-neutral-300">Celkem ticketů:</span>
                                        <span class="text-neutral-900 dark:text-white">3</span>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700">
                                    <button
                                        class="w-full px-3 py-2 text-sm bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600">
                                        Zobrazit profil
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div
                            class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Rychlé akce</h3>

                            <div class="space-y-2">
                                <button
                                    class="w-full px-3 py-2 text-sm bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50">
                                    Označit jako vyřešené
                                </button>
                                <button
                                    class="w-full px-3 py-2 text-sm bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900/50">
                                    Eskalovat
                                </button>
                                <button
                                    class="w-full px-3 py-2 text-sm bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50">
                                    Uzavřít ticket
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, computed } from 'vue';

// Accept the ticket ID from the route
const props = defineProps<{
    id: string,
    ticket: any,
    messages: any[],
}>();

const ticket = ref(props.ticket);
const messages = ref(props.messages);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tickets', href: '/tickets' },
    { title: `#${ticket.value.id}`, href: '#' },
];

const messagesContainer = ref<HTMLElement>();
const replyText = ref('');
const markAsResolved = ref(false);
const notifyUser = ref(true);

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const sendReply = async () => {
    if (!replyText.value.trim()) return;

    try {
        const response = await fetch(`/tickets/${ticket.value.id}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                message: replyText.value,
                markAsResolved: markAsResolved.value,
            }),
        });

        if (!response.ok) throw new Error('Failed to send reply');

        const data = await response.json();

        messages.value.push({
            id: data.id,
            content: data.content,
            author: data.author,
            timestamp: data.timestamp,
            isAdmin: data.isAdmin,
        });

        // Update ticket status if changed
        if (data.status && ticket.value.status !== data.status) {
            ticket.value.status = data.status;
        }

        replyText.value = '';
        markAsResolved.value = false;
        scrollToBottom();
    } catch (e) {
        alert('Odpověď se nepodařilo odeslat.');
    }
};

const updateStatus = () => {
    // API call to update status
    console.log('Status updated:', ticket.value.status);
};

const updatePriority = () => {
    // API call to update priority
    console.log('Priority updated:', ticket.value.priority);
};

const updateCategory = () => {
    // API call to update category
    console.log('Category updated:', ticket.value.category);
};

const updateAssignee = () => {
    // API call to update assignee
    console.log('Assignee updated:', ticket.value.assignedTo);
};

const getStatusClass = (status: string) => {
    const classes = {
        'open': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        'in_progress': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        'resolved': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'closed': 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
    };
    return classes[status] || classes.open;
};

const getStatusText = (status: string) => {
    const texts = {
        'open': 'Otevřené',
        'in_progress': 'V řešení',
        'resolved': 'Vyřešené',
        'closed': 'Uzavřené'
    };
    return texts[status] || 'Neznámé';
};

const getPriorityClass = (priority: string) => {
    const classes = {
        'low': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'medium': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        'high': 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
        'urgent': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
    };
    return classes[priority] || classes.medium;
};

const getPriorityText = (priority: string) => {
    const texts = {
        'low': 'Nízká',
        'medium': 'Střední',
        'high': 'Vysoká',
        'urgent': 'Urgentní'
    };
    return texts[priority] || 'Střední';
};

const getCategoryText = (category: string) => {
    const texts = {
        'technical': 'Technické',
        'gameplay': 'Gameplay',
        'player_report': 'Nahlášení hráče',
        'other': 'Ostatní'
    };
    return texts[category] || 'Ostatní';
};

const getAvatarUrl = (message: any) => {
    // If admin, use default icon, else use MCHeads with author name
    if (message.isAdmin) {
        return null;
    }
    // Use MCHeads API, 40px size, no helm
    return `https://mc-heads.net/avatar/${encodeURIComponent(message.author)}/40`;
};

onMounted(() => {
    scrollToBottom();
});
</script>