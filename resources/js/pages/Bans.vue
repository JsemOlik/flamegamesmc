<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Search, AlertCircle, Clock, CheckCircle, Ban } from 'lucide-vue-next';

// Helper to build MC head avatar URL
const mcHeadUrl = (name: string, size = 40) =>
    `https://mc-heads.net/avatar/${encodeURIComponent(name)}/${size}`;

// OPTIONAL: fallback image path if avatar fails to load
const fallbackSrc = '/FlameGames.webp';

const bans = ref([
    {
        id: 1,
        player: 'JsemOlik',
        // avatar fields not used anymore; kept for backward compatibility
        avatar: '',
        bannedBy: 'Notch',
        bannedByAvatar: '',
        reason: 'Griefing spawn area and destroying player builds',
        bannedOn: '2025-01-10T14:30:00Z',
        bannedUntil: '2025-01-17T14:30:00Z',
        status: 'ACTIVE',
        type: 'Temporary',
        severity: 'Zásadní',
    },
    {
        id: 2,
        player: 'zaycz',
        avatar: '',
        bannedBy: 'Nim429',
        bannedByAvatar: '',
        reason: 'Using X-ray hacks and fly modifications',
        bannedOn: '2025-01-09T08:15:00Z',
        bannedUntil: null,
        status: 'ACTIVE',
        type: 'Permanent',
        severity: 'Vážný',
    },
    {
        id: 3,
        player: 'ToxicPlayer123',
        avatar: '',
        bannedBy: 'BadMonster',
        bannedByAvatar: '',
        reason: 'Harassment and inappropriate language in chat',
        bannedOn: '2025-01-09T16:45:00Z',
        bannedUntil: '2025-01-12T16:45:00Z',
        status: 'ACTIVE',
        type: 'Temporary',
        severity: 'Menší',
    },
]);

const stats = computed(() => ({
    activeBans: bans.value.filter((b) => b.status === 'ACTIVE').length,
    permanentBans: bans.value.filter((b) => b.type === 'Permanent').length,
    todaysBans: bans.value.filter((b) => {
        const today = new Date().toDateString();
        return new Date(b.bannedOn).toDateString() === today;
    }).length,
    fairPlayRate: 99.2,
}));

const searchQuery = ref('');
// NOTE: your filters show Czech labels, but your data uses English keys (Temporary/Permanent + MINOR/MAJOR/SEVERE).
// To keep your UI text while making filters work, we map the selected UI label to data value.
const selectedType = ref('Všechny Důvody');
const selectedSeverity = ref('Všechny Úrovně');

const typeMap: Record<string, string | null> = {
    'Všechny Důvody': null,
    Dočasné: 'Temporary',
    'Permanentí': 'Permanent',
};

const severityMap: Record<string, string | null> = {
    'Všechny Úrovně': null,
    Menší: 'Menší',
    Zásadní: 'Zásadní',
    Vážný: 'Vážný',
};

const filteredBans = computed(() =>
    bans.value.filter((b) => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch =
            b.player.toLowerCase().includes(q) ||
            b.reason.toLowerCase().includes(q) ||
            b.bannedBy.toLowerCase().includes(q);

        const mappedType = typeMap[selectedType.value] ?? null;
        const mappedSeverity = severityMap[selectedSeverity.value] ?? null;

        const matchesType = mappedType === null || b.type === mappedType;
        const matchesSeverity = mappedSeverity === null || b.severity === mappedSeverity;

        return matchesSearch && matchesType && matchesSeverity;
    })
);

const formatDate = (s: string) =>
    new Date(s).toLocaleString('cs-CZ', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

// Optional inline error handler to fallback to server logo if a head fails to load
const onImgError = (e: Event) => {
    const img = e.target as HTMLImageElement;
    if (img && img.src !== location.origin + fallbackSrc) {
        img.src = fallbackSrc;
    }
};
</script>

<template>

    <Head title="Recent Bans">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <!-- Page wrapper with the SAME background layer as Welcome.vue -->
    <div class="relative flex min-h-screen flex-col items-center pt-20 lg:pt-24">
        <!-- Background layer -->
        <div class="absolute inset-0 z-0 bg-black" :style="{
            backgroundImage: `url('/header.png')`,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            opacity: 0.1,
        }" aria-hidden="true"></div>

        <!-- Fixed header -->
        <header
            class="fixed top-0 left-0 right-0 z-50 bg-transparent backdrop-blur supports-[backdrop-filter]:bg-black/0">
            <div class="mx-auto w-full max-w-4xl px-6 lg:px-8 py-3">
                <nav class="grid grid-cols-3 items-center">
                    <div></div>

                    <div class="flex items-center justify-center gap-6 text-sm">
                        <Link :href="route('home')" class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">
                        Home
                        </Link>
                        <Link :href="route('bany')" class="px-1 py-1.5 leading-normal text-red-400 font-medium">
                        Bany
                        </Link>
                        <Link :href="route('staff')" class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">
                        Staff
                        </Link>
                        <Link href="/discord" class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">
                        Discord
                        </Link>
                    </div>

                    <div class="flex items-center justify-end gap-4 text-sm">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">
                        Tickety
                        </Link>
                        <template v-else>
                            <Link :href="route('login')"
                                class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">
                            Přihlásit se
                            </Link>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Content container on top of background -->
        <section
            class="relative z-10 w-full text-white flex items-start justify-center min-h-[calc(100vh-4.5rem)] lg:min-h-[calc(100vh-6rem)]">
            <div class="mx-auto w-full max-w-7xl px-4 lg:px-8 py-10">
                <!-- Badge + Title -->
                <div class="flex justify-center mb-6">
                    <div
                        class="bg-red-600/20 text-red-400 px-4 py-2 rounded-full text-sm font-medium border border-red-600/30">
                        Moderace Serveru
                    </div>
                </div>
                <h1 class="text-4xl lg:text-5xl font-bold text-center mb-3">
                    Nedávné Tresty
                </h1>
                <p class="text-center text-white/70 mb-10 max-w-2xl mx-auto">
                    Transparentní v moderaci - zobrazte si nedávné bany které udělil náš
                    A-Tým.
                </p>

                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-neutral-900/70 border border-red-600/20 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <Ban class="w-6 h-6 text-red-400" />
                        </div>
                        <div class="text-2xl font-bold text-red-400">
                            {{ stats.activeBans }}
                        </div>
                        <div class="text-sm text-white/70">Aktivní Bany</div>
                    </div>

                    <div class="bg-neutral-900/70 border border-orange-600/20 rounded-xl p-6 text-center">
                        <div
                            class="w-12 h-12 bg-orange-600/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <AlertCircle class="w-6 h-6 text-orange-400" />
                        </div>
                        <div class="text-2xl font-bold text-orange-400">
                            {{ stats.permanentBans }}
                        </div>
                        <div class="text-sm text-white/70">Permanentní Bany</div>
                    </div>

                    <div class="bg-neutral-900/70 border border-yellow-600/20 rounded-xl p-6 text-center">
                        <div
                            class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <Clock class="w-6 h-6 text-yellow-400" />
                        </div>
                        <div class="text-2xl font-bold text-yellow-400">
                            {{ stats.todaysBans }}
                        </div>
                        <div class="text-sm text-white/70">Dnešní Bany</div>
                    </div>

                    <div class="bg-neutral-900/70 border border-green-600/20 rounded-xl p-6 text-center">
                        <div
                            class="w-12 h-12 bg-green-600/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <CheckCircle class="w-6 h-6 text-green-400" />
                        </div>
                        <div class="text-2xl font-bold text-green-400">
                            {{ stats.fairPlayRate }}%
                        </div>
                        <div class="text-sm text-white/70">Fair Play Procentuál</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-neutral-900/60 rounded-xl p-6 mb-8 border border-white/5">
                    <div class="flex flex-col lg:flex-row gap-4 items-center">
                        <div class="relative flex-1 max-w-md">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-white/50" />
                            <input v-model="searchQuery" type="text" placeholder="Vyhledej hráče, důvod, nebo staff..."
                                class="w-full bg-neutral-800/70 border border-white/10 rounded-lg pl-10 pr-4 py-3 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                        </div>

                        <select v-model="selectedType"
                            class="bg-neutral-800/70 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option>Všechny Důvody</option>
                            <option>Dočasné</option>
                            <option>Permanentí</option>
                        </select>

                        <select v-model="selectedSeverity"
                            class="bg-neutral-800/70 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option>Všechny Úrovně</option>
                            <option>Menší</option>
                            <option>Zásadní</option>
                            <option>Vážný</option>
                        </select>

                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            Unban Dotazník
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-neutral-900/60 rounded-xl overflow-hidden border border-white/5">
                    <div class="bg-neutral-900/80 px-6 py-4">
                        <div class="grid grid-cols-6 gap-4 text-sm font-medium text-white/70 uppercase tracking-wider">
                            <div>Hráč</div>
                            <div>Zabanoval</div>
                            <div>Důvod</div>
                            <div>Zabanován dne</div>
                            <div>Zabanován do</div>
                            <div>Status</div>
                        </div>
                    </div>

                    <div class="divide-y divide-white/5">
                        <div v-for="ban in filteredBans" :key="ban.id"
                            class="px-6 py-4 hover:bg-white/5 transition-colors">
                            <div class="grid grid-cols-6 gap-4 items-center">
                                <div class="flex items-center gap-3">
                                    <img :src="mcHeadUrl(ban.player, 40)" :alt="ban.player" class="w-10 h-10 rounded-lg"
                                        referrerpolicy="no-referrer" @error="onImgError" />
                                    <div>
                                        <div class="font-medium text-white">{{ ban.player }}</div>
                                        <div class="text-sm text-white/60">{{ ban.severity }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <img :src="mcHeadUrl(ban.bannedBy, 32)" :alt="ban.bannedBy"
                                        class="w-8 h-8 rounded-lg" referrerpolicy="no-referrer" @error="onImgError" />
                                    <div class="text-sm text-white/80">{{ ban.bannedBy }}</div>
                                </div>

                                <div class="text-sm text-white/80 max-w-xs truncate" :title="ban.reason">
                                    {{ ban.reason }}
                                </div>

                                <div class="text-sm text-white/80">
                                    {{ formatDate(ban.bannedOn) }}
                                </div>

                                <div class="text-sm">
                                    <span v-if="ban.bannedUntil" class="text-white/80">
                                        {{ formatDate(ban.bannedUntil) }}
                                    </span>
                                    <span v-else class="text-red-400 font-medium">Permanentí</span>
                                </div>

                                <div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-600/20 text-red-400 border border-red-600/30">
                                        {{ ban.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="filteredBans.length === 0" class="px-6 py-12 text-center">
                        <div class="text-white/80 text-lg">
                            Nenašli jsme žádný ban podle vaších kritérií
                        </div>
                        <div class="text-white/60 text-sm mt-2">
                            Zkuste upravit vaše vyhledávání nebo filtry
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>