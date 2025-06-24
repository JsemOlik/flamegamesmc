<template>
    <Head title="Uživatelé" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-neutral-50 dark:bg-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header -->
                <div class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Uživatelé</h1>
                        <p class="mt-2 text-neutral-600 dark:text-neutral-300">
                            Správa registrovaných uživatelů, změna hesla a role
                        </p>
                    </div>
                </div>

                <!-- Users List -->
                <div class="bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Seznam uživatelů</h2>
                    </div>
                    <div>
                        <ul>
                            <li v-for="user in users" :key="user.id" @click="openUserPanel(user)"
                                class="flex items-center gap-4 px-6 py-4 cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-all border-b border-neutral-100 dark:border-neutral-700 last:border-b-0">
                                <div class="w-12 h-12">
                                    <img :src="`https://mc-heads.net/avatar/${encodeURIComponent(user.name)}/40`" :alt="user.name" class="w-full h-full object-cover rounded-lg bg-neutral-200 dark:bg-neutral-700" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-neutral-900 dark:text-white text-lg">{{ user.name }}</span>
                                        <span :class="user.role === 'admin' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ user.role === 'admin' ? 'Admin' : 'Hráč' }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-300 truncate">{{ user.email }}</div>
                                    <div class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Login kód: <span class="font-mono">{{ user.code || '—' }}</span></div>
                                </div>
                                <div class="text-xs text-neutral-400 dark:text-neutral-500 text-right min-w-[100px]">
                                    Registrován: <span class="font-medium text-neutral-700 dark:text-neutral-200">{{ user.created_at }}</span>
                                </div>
                                <svg class="w-5 h-5 text-neutral-300 dark:text-neutral-500 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- User Drawer/Panel -->
                <transition name="fade">
                <div v-if="showUserPanel" class="fixed inset-0 z-50 flex">
                    <div class="fixed inset-0 backdrop-blur-sm bg-white/30 dark:bg-neutral-900/30 transition-opacity" @click="closeUserPanel"></div>
                    <div class="relative ml-auto w-full max-w-md bg-white dark:bg-neutral-900 h-full shadow-2xl animate-in slide-in-from-right duration-200 flex flex-col">
                        <div class="px-8 py-8 border-b border-neutral-200 dark:border-neutral-700 flex flex-col items-center">
                            <div class="w-24 h-24 mb-4">
                                <img :src="selectedUser ? `https://mc-heads.net/avatar/${encodeURIComponent(selectedUser.name)}/80` : ''" :alt="selectedUser?.name" class="w-full h-full object-cover rounded-lg bg-neutral-200 dark:bg-neutral-700" />
                            </div>
                            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white mb-1">{{ selectedUser?.name }}</h2>
                            <div class="text-sm text-neutral-500 dark:text-neutral-300 mb-2">{{ selectedUser?.email }}</div>
                            <div class="text-xs text-neutral-400 dark:text-neutral-500 mb-2">Login kód: <span class="font-mono">{{ selectedUser?.code || '—' }}</span></div>
                            <span :class="selectedUser?.role === 'admin' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'" class="px-2 py-1 text-xs font-medium rounded-full mb-2">
                                {{ selectedUser?.role === 'admin' ? 'Admin' : 'Hráč' }}
                            </span>
                        </div>
                        <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
                            <!-- Dummy Stats -->
                            <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4 border border-neutral-200 dark:border-neutral-700 flex items-center gap-6">
                                <div class="flex-1">
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400">Tickety vytvořené</div>
                                    <div class="text-xl font-bold text-neutral-900 dark:text-white">{{ selectedUser?.stats.ticketsCreated }}</div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400">Poslední přihlášení</div>
                                    <div class="text-xl font-bold text-neutral-900 dark:text-white">{{ selectedUser?.stats.lastLogin }}</div>
                                </div>
                            </div>
                            <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4 border border-neutral-200 dark:border-neutral-700">
                                <div class="text-xs text-neutral-500 dark:text-neutral-400 mb-1">Registrován</div>
                                <div class="text-base font-medium text-neutral-900 dark:text-white">{{ selectedUser?.created_at }}</div>
                            </div>
                            <!-- Actions -->
                            <div class="space-y-4">
                                <Button class="w-full" @click="openCodeModal(selectedUser)">Změnit Login Kód</Button>
                                <Button class="w-full" @click="openRoleModal(selectedUser)">Změnit roli</Button>
                                <Button class="w-full" variant="destructive" @click="openDeleteModal(selectedUser)">Smazat uživatele</Button>
                            </div>
                        </div>
                        <div class="px-8 py-4 border-t border-neutral-200 dark:border-neutral-700 flex justify-end">
                            <Button variant="outline" @click="closeUserPanel">Zavřít</Button>
                        </div>
                    </div>
                </div>
                </transition>

                <!-- Code Modal (replaces Password Modal) -->
                <div v-if="showCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-in fade-in duration-200">
                    <div class="bg-white dark:bg-neutral-800 rounded-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto animate-in zoom-in-95 duration-200">
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Změnit Login Kód pro {{ selectedUser?.name }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <Label for="new_code">Nový Login Kód</Label>
                                <Input id="new_code" v-model="codeForm.code" type="text" placeholder="Nový Login Kód" />
                                <InputError :message="codeForm.error" />
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex justify-end space-x-3">
                            <Button @click="closeCodeModal" variant="outline">Zrušit</Button>
                            <Button @click="changeCode" :disabled="codeForm.processing">Uložit</Button>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-in fade-in duration-200">
                    <div class="bg-white dark:bg-neutral-800 rounded-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto animate-in zoom-in-95 duration-200">
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Smazat uživatele</h3>
                        </div>
                        <div class="p-6">
                            <p>Opravdu chcete smazat tohoto uživatele? Tato akce je nevratná.</p>
                        </div>
                        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex justify-end space-x-3">
                            <Button @click="closeDeleteModal" variant="outline">Zrušit</Button>
                            <Button @click="deleteUser" variant="destructive" :disabled="deleteForm.processing">Smazat</Button>
                        </div>
                    </div>
                </div>

                <!-- Role Modal -->
                <div v-if="showRoleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-in fade-in duration-200">
                    <div class="bg-white dark:bg-neutral-800 rounded-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto animate-in zoom-in-95 duration-200">
                        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Změnit roli pro {{ selectedUser?.name }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <Label for="role">Role</Label>
                                <select id="role" v-model="roleForm.role" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white px-3 py-2">
                                    <option value="player">Hráč</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex justify-end space-x-3">
                            <Button @click="closeRoleModal" variant="outline">Zrušit</Button>
                            <Button @click="changeRole">Uložit</Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { type BreadcrumbItem, type User } from '@/types';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Button from '@/components/ui/button/Button.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Uživatelé', href: '/users' },
];

// Accept users as a prop from Inertia
const props = defineProps<{ users?: User[] }>();

const users = ref((props.users ?? []).map(user => ({
    ...user,
    stats: {
        ticketsCreated: Math.floor(Math.random() * 100),
        lastLogin: user.updated_at ? user.updated_at.substring(0, 10) : 'N/A',
    }
})));

const showUserPanel = ref(false);
const selectedUser = ref<any | null>(null);

function openUserPanel(user: any) {
    selectedUser.value = user;
    showUserPanel.value = true;
}
function closeUserPanel() {
    showUserPanel.value = false;
    selectedUser.value = null;
}

const showCodeModal = ref(false);
const showDeleteModal = ref(false);

const codeForm = ref({
    code: '',
    error: '',
    processing: false,
});
const deleteForm = ref({
    processing: false,
});

function openCodeModal(user: any) {
    selectedUser.value = user;
    codeForm.value = { code: '', error: '', processing: false };
    showCodeModal.value = true;
}
function closeCodeModal() {
    showCodeModal.value = false;
}
async function changeCode() {
    codeForm.value.processing = true;
    codeForm.value.error = '';
    if (!codeForm.value.code) {
        codeForm.value.error = 'Login kód nesmí být prázdný.';
        codeForm.value.processing = false;
        return;
    }
    try {
        const res = await axios.post(`/users/${selectedUser.value.id}/change-code`, {
            code: codeForm.value.code,
        });
        // Update the user's code in the local list only if backend succeeded
        const idx = users.value.findIndex(u => u.id === selectedUser.value?.id);
        if (idx !== -1 && res.data && res.data.code) {
            users.value[idx].code = res.data.code;
            if (selectedUser.value) selectedUser.value.code = res.data.code;
        }
        showCodeModal.value = false;
    } catch (e) {
        codeForm.value.error = 'Chyba při změně login kódu.';
    } finally {
        codeForm.value.processing = false;
    }
}

function openDeleteModal(user: any) {
    selectedUser.value = user;
    showDeleteModal.value = true;
}
function closeDeleteModal() {
    showDeleteModal.value = false;
}
async function deleteUser() {
    deleteForm.value.processing = true;
    try {
        await axios.delete(`/users/${selectedUser.value.id}`);
        // Remove user from local list
        users.value = users.value.filter(u => u.id !== selectedUser.value.id);
        closeDeleteModal();
        closeUserPanel();
    } catch (e) {
        alert('Chyba při mazání uživatele.');
    } finally {
        deleteForm.value.processing = false;
    }
}

const showRoleModal = ref(false);

const roleForm = ref({
    role: 'player',
});

function openRoleModal(user: any) {
    selectedUser.value = user;
    roleForm.value.role = user.role || 'player';
    showRoleModal.value = true;
}
function closeRoleModal() {
    showRoleModal.value = false;
}
async function changeRole() {
    if (!selectedUser.value) return;
    try {
        const res = await axios.post(`/users/${selectedUser.value.id}/change-role`, {
            role: roleForm.value.role,
        });
        // Update the user's role in the local list only if backend succeeded
        const idx = users.value.findIndex(u => u.id === selectedUser.value?.id);
        if (idx !== -1 && res.data && res.data.role) {
            users.value[idx].role = res.data.role;
        }
        showRoleModal.value = false;
    } catch (e) {
        alert('Chyba při změně role.');
    }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.slide-in-from-right {
  animation: slideInFromRight 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
@keyframes slideInFromRight {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}
</style>
