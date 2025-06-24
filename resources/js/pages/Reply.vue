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
                        <select v-if="isAdmin" v-model="ticket.status" @change="updateStatus"
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
                        <!-- User Info -->
                        <div
                            v-if="isAdmin"
                            class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Informace o uživateli</h3>

                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <img :src="userData.avatarUrl" :alt="userData.username" 
                                        class="w-10 h-10 rounded-lg bg-neutral-200 dark:bg-neutral-700" />
                                    <div>
                                        <p class="font-medium text-neutral-900 dark:text-white">{{ userData.username }}</p>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Hráč</p>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-neutral-600 dark:text-neutral-300">Celkem ticketů:</span>
                                        <span class="text-neutral-900 dark:text-white">{{ userData.ticketCount }}</span>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700">
                                    <button
                                        class="w-full px-3 py-2 text-sm bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-all duration-200">
                                        Zobrazit profil
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Participants Section -->
                        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Účastníci</h3>
                                <button v-if="isAdmin || isOwner" @click="showAddParticipantModal = true"
                                    class="text-sm px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                    Přidat
                                </button>
                            </div>

                            <div class="space-y-3">
                                <!-- Show admin at the top if they have sent a message -->
                                <div v-if="messages.some(m => m.isAdmin)" class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-neutral-900 dark:text-white">
                                                Admin
                                                <span class="ml-2 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-0.5 rounded">
                                                    Admin
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Existing participants list -->
                                <div v-for="participant in participants" :key="participant.username" 
                                    class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <img :src="participant.avatarUrl" :alt="participant.username"
                                            class="w-8 h-8 rounded-lg bg-neutral-200 dark:bg-neutral-700" />
                                        <div>
                                            <p class="font-medium text-neutral-900 dark:text-white">
                                                {{ participant.username }}
                                                <span v-if="participant.role === 'owner'" 
                                                    class="ml-2 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-0.5 rounded">
                                                    Vlastník
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <button v-if="canManageParticipants && participant.role !== 'owner'"
                                        @click="askRemoveParticipant(participant.username)"
                                        class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                        Odebrat
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Info -->
                        <div
                            v-if="isAdmin"
                            class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Informace o ticketu
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Priorita</label>
                                    <select v-if="isAdmin" v-model="ticket.priority" @change="updatePriority"
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
                                    <select v-if="isAdmin" v-model="ticket.category" @change="updateCategory"
                                        class="w-full rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white px-3 py-2">
                                        <option value="technical">Technické</option>
                                        <option value="gameplay">Gameplay</option>
                                        <option value="player_report">Nahlášení hráče</option>
                                        <option value="other">Ostatní</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div
                            v-if="isAdmin || isOwner"
                            class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Rychlé akce</h3>

                            <div class="space-y-2">
                                <button @click="resolveTicket"
                                    class="w-full px-3 py-2 text-sm bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition-all duration-200">
                                    Označit jako vyřešené
                                </button>
                                <button @click="deleteTicket"
                                    class="w-full px-3 py-2 text-sm bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-all duration-200">
                                    Smazat ticket
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Confirmation Modal -->
    <div v-if="showConfirmModal"
        class="fixed inset-0 backdrop-blur-sm bg-white/30 dark:bg-neutral-900/30 flex items-center justify-center z-50 animate-in fade-in duration-200">
        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-lg max-w-sm w-full p-6 text-center animate-in zoom-in-95 duration-200" @click.stop>
            <h3 class="text-lg font-semibold mb-4 text-neutral-900 dark:text-white">Potvrzení Akce</h3>
            <p class="mb-6 text-neutral-700 dark:text-neutral-300">
                Opravdu chcete
                <span class="font-bold" :class="{
                    'text-green-600 dark:text-green-400': actionToConfirm === 'resolve',
                    'text-red-600 dark:text-red-400': actionToConfirm === 'delete'
                }">
                    {{ actionToConfirm === 'resolve' ? 'vyřešit' : 'smazat' }}
                </span>
                tento ticket?
            </p>
            <div class="flex justify-center gap-6">
                <button @click="confirmAction"
                    :class="[
                        'px-4 py-2 text-white rounded-md font-semibold transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg',
                        actionToConfirm === 'resolve' 
                            ? 'bg-green-600 hover:bg-green-700' 
                            : 'bg-red-600 hover:bg-red-700'
                    ]">
                    Potvrdit
                </button>
                <button @click="cancelAction"
                    class="px-4 py-2 bg-neutral-600 hover:bg-neutral-500 text-white rounded-md font-semibold transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg">
                    Zrušit
                </button>
            </div>
        </div>
    </div>

    <!-- Add Participant Modal -->
    <div v-if="showAddParticipantModal"
        class="fixed inset-0 backdrop-blur-sm bg-white/30 dark:bg-neutral-900/30 flex items-center justify-center z-50 animate-in fade-in duration-200">
        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-lg max-w-sm w-full p-6 animate-in zoom-in-95 duration-200">
            <h3 class="text-lg font-semibold mb-4 text-neutral-900 dark:text-white">Přidat účastníka</h3>
            
            <form @submit.prevent="addParticipant">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                        Uživatelské jméno
                    </label>
                    <input v-model="newParticipantUsername" type="text" required
                        class="w-full rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white px-3 py-2">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showAddParticipantModal = false"
                        class="px-4 py-2 border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700">
                        Zrušit
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        Přidat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Remove Participant Modal -->
    <div v-if="showRemoveParticipantModal"
        class="fixed inset-0 backdrop-blur-sm bg-white/30 dark:bg-neutral-900/30 flex items-center justify-center z-50 animate-in fade-in duration-200">
        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-lg max-w-sm w-full p-6 animate-in zoom-in-95 duration-200">
            <h3 class="text-lg font-semibold mb-4 text-neutral-900 dark:text-white">Potvrzení odebrání</h3>
            <p class="mb-6 text-neutral-700 dark:text-neutral-300">
                Opravdu chcete odebrat uživatele <span class="font-bold">{{ participantToRemove }}</span> z ticketu?
            </p>
            <div class="flex justify-center gap-6">
                <button @click="confirmRemoveParticipant"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold transition-all duration-200">
                    Potvrdit
                </button>
                <button @click="showRemoveParticipantModal = false"
                    class="px-4 py-2 bg-neutral-600 hover:bg-neutral-500 text-white rounded-md font-semibold transition-all duration-200">
                    Zrušit
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, computed } from 'vue';
import axios from 'axios';


// Accept the ticket ID from the route
const props = defineProps<{
    id: string,
    ticket: any,
    messages: any[],
    participants: any[],
    userData: {
        username: string,
        ticketCount: number,
        avatarUrl: string,
    },
    canManageParticipants: boolean,
    currentUserRole: string,
    currentUsername: string,
}>();

const ticket = ref(props.ticket);
const messages = ref(props.messages);
const userData = ref(props.userData);
const participants = ref(props.participants);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tickets', href: '/tickets' },
    { title: `#${ticket.value.id}`, href: '#' },
];

const messagesContainer = ref<HTMLElement>();
const replyText = ref('');
const notifyUser = ref(true);

const showConfirmModal = ref(false);
const actionToConfirm = ref<'resolve' | 'delete' | null>(null);
const showAddParticipantModal = ref(false);
const newParticipantUsername = ref('');
const showRemoveParticipantModal = ref(false);
const participantToRemove = ref(null);

const isAdmin = computed(() => props.currentUserRole === 'admin');
const isOwner = computed(() => props.ticket.username === props.currentUsername);
const isParticipant = computed(() => !isAdmin.value && !isOwner.value);

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const resolveTicket = () => {
    actionToConfirm.value = 'resolve';
    showConfirmModal.value = true;
};

const deleteTicket = () => {
    actionToConfirm.value = 'delete';
    showConfirmModal.value = true;
};

const confirmAction = async () => {
    if (!actionToConfirm.value) return;

    try {
        if (actionToConfirm.value === 'resolve') {
            await axios.patch(`/tickets/${ticket.value.id}/status`, { status: 'resolved' });
            ticket.value.status = 'resolved';
        } else if (actionToConfirm.value === 'delete') {
            await axios.delete(`/tickets/${ticket.value.id}`);
            window.location.href = '/tickets';
            return;
        }
    } catch (e) {
        alert(`Nepodařilo se ${actionToConfirm.value === 'resolve' ? 'vyřešit' : 'smazat'} ticket.`);
    } finally {
        showConfirmModal.value = false;
        actionToConfirm.value = null;
    }
};

const cancelAction = () => {
    showConfirmModal.value = false;
    actionToConfirm.value = null;
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

        replyText.value = '';
        scrollToBottom();
    } catch (e) {
        alert('Odpověď se nepodařilo odeslat.');
    }
};

const updateStatus = async () => {
    try {
        await axios.patch(`/tickets/${ticket.value.id}/status`, { status: ticket.value.status });
    } catch (e) {
        alert('Nepodařilo se změnit status.');
    }
};

const updatePriority = async () => {
    try {
        await axios.patch(`/tickets/${ticket.value.id}/priority`, { priority: ticket.value.priority });
    } catch (e) {
        alert('Nepodařilo se změnit prioritu.');
    }
};

const updateCategory = () => {
    // API call to update category
    console.log('Category updated:', ticket.value.category);
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

const addParticipant = async () => {
    if (!newParticipantUsername.value.trim()) return;

    try {
        await axios.post(`/tickets/${ticket.value.id}/participants`, {
            username: newParticipantUsername.value
        });
        window.location.reload();
    } catch (e) {
        alert('Nepodařilo se přidat účastníka.');
    } finally {
        showAddParticipantModal.value = false;
        newParticipantUsername.value = '';
    }
};

const askRemoveParticipant = (username) => {
    participantToRemove.value = username;
    showRemoveParticipantModal.value = true;
};

const confirmRemoveParticipant = async () => {
    if (!participantToRemove.value) return;
    try {
        const response = await axios({
            method: 'delete',
            url: `/tickets/${props.id}/participants`,
            data: { username: participantToRemove.value }
        });
        // Remove the participant from the local state
        participants.value = participants.value.filter(p => p.username !== participantToRemove.value);
        showRemoveParticipantModal.value = false;
        participantToRemove.value = null;
    } catch (e) {
        alert('Nepodařilo se odebrat účastníka.');
        showRemoveParticipantModal.value = false;
        participantToRemove.value = null;
    }
};

onMounted(() => {
    scrollToBottom();
});
</script>