<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Users,
    Zap,
    HeartHandshake,
    Medal,
    Circle,
    Star,
    Wrench,
    Code2,
    ShieldHalf,
    HelpCircle,
    Crown,
    Trophy,
    Calendar
} from 'lucide-vue-next';

type Role =
    | 'OWNER'
    | 'CO_OWNER'
    | 'ADMIN'
    | 'TEAM_LEADER'
    | 'DEV'
    | 'BUILDER'
    | 'EVENTER'
    | 'HELPER';

interface StaffMember {
    id: number;
    username: string; // Minecraft nick used for MC-Heads
    role: Role;
    title: string;
    bio: string;
    online: boolean;
    helped?: number;
    years?: number;
    color?: string;
}

const filters = ref<Role | 'ALL'>('ALL');

const staff = ref<StaffMember[]>([
    {
        id: 1,
        username: 'Nim429',
        role: 'OWNER',
        title: 'Vlastník serveru',
        bio: 'Zakladatel a vizionář QPvP. Dozor nad provozem a rozvojem komunity.',
        online: true,
        helped: 5200,
        years: 8,
        color: 'rose'
    },
    {
        id: 2,
        username: 'zaycz',
        role: 'CO_OWNER',
        title: 'Spoluvlastník',
        bio: 'Podílí se na vedení týmu a strategických rozhodnutích.',
        online: true,
        helped: 4100,
        years: 6,
        color: 'fuchsia'
    },
    {
        id: 3,
        username: 'Hakki',
        role: 'ADMIN',
        title: 'Administrátor',
        bio: 'Dohled nad týmem a řešení složitějších případů.',
        online: true,
        helped: 3500,
        years: 5,
        color: 'amber'
    },
    {
        id: 4,
        username: 'BATboy',
        role: 'TEAM_LEADER',
        title: 'Team Leader',
        bio: 'Koordinace týmu, plánování směn a plynulá komunikace.',
        online: false,
        helped: 2600,
        years: 4,
        color: 'cyan'
    },
    {
        id: 5,
        username: 'JsemOlik',
        role: 'DEV',
        title: 'Vývojář',
        bio: 'Pluginy, optimalizace a nové herní mechaniky.',
        online: true,
        helped: 1400,
        years: 3,
        color: 'emerald'
    },
    {
        id: 6,
        username: 'Marci',
        role: 'DEV',
        title: 'Vývojář',
        bio: 'Backend, stabilita a integrace systémů.',
        online: false,
        helped: 900,
        years: 3,
        color: 'teal'
    },
    {
        id: 7,
        username: 'Honzaak',
        role: 'BUILDER',
        title: 'Stavitel',
        bio: 'Design map, lobby a tematických arén.',
        online: true,
        helped: 750,
        years: 3,
        color: 'yellow'
    },
    {
        id: 8,
        username: 'MATT11',
        role: 'BUILDER',
        title: 'Stavitel',
        bio: 'Detailní buildy a úpravy prostředí pro PvP.',
        online: false,
        helped: 620,
        years: 2,
        color: 'lime'
    },
    {
        id: 9,
        username: 'Proste_Tommy',
        role: 'EVENTER',
        title: 'Eventer',
        bio: 'Organizace eventů, soutěží a speciálních víkendových akcí.',
        online: true,
        helped: 830,
        years: 2,
        color: 'orange'
    }
]);

const filteredStaff = computed(() =>
    filters.value === 'ALL'
        ? staff.value
        : staff.value.filter((m) => m.role === filters.value)
);

const stats = computed(() => {
    const members = staff.value.length;
    const online = staff.value.filter((m) => m.online).length;
    const helped = staff.value.reduce((s, m) => s + (m.helped ?? 0), 0);
    const avgYears =
        members === 0
            ? 0
            : Math.round(
                (staff.value.reduce((s, m) => s + (m.years ?? 0), 0) / members) * 10
            ) / 10;
    return { members, online, helped, avgYears };
});

const roleLabel: Record<Role, string> = {
    OWNER: 'OWNER',
    CO_OWNER: 'CO-OWNER',
    ADMIN: 'ADMIN',
    TEAM_LEADER: 'TEAM LEADER',
    DEV: 'DEV',
    BUILDER: 'BUILDER',
    EVENTER: 'EVENTER',
    HELPER: 'HELPER'
};

const roleOrder: Record<Role, number> = {
    OWNER: 1,
    CO_OWNER: 2,
    ADMIN: 3,
    TEAM_LEADER: 4,
    DEV: 5,
    BUILDER: 6,
    EVENTER: 7,
    HELPER: 8
};

// MC-Heads helpers
function avatarUrl(username: string, size = 64) {
    return `https://mc-heads.net/avatar/${encodeURIComponent(username)}/${size}`;
}
function headRenderUrl(username: string, size = 64) {
    return `https://mc-heads.net/head/${encodeURIComponent(username)}/${size}`;
}
</script>

<template>

    <Head title="Server Staff">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <!-- Same wrapper; background not zoomed (contain keeps full image in view) -->
    <div class="relative flex min-h-screen flex-col items-center pt-20 lg:pt-24">
        <div class="absolute inset-0 z-0 bg-black" :style="{
            backgroundImage: `url('/header.png')`,
            backgroundSize: 'cover', // fills from top to bottom
            backgroundPosition: 'top center', // anchors the image to the top edge
            backgroundRepeat: 'no-repeat',
            opacity: 0.1 // same visual as other pages
        }" aria-hidden="true"></div>

        <!-- Header -->
        <header
            class="fixed top-0 left-0 right-0 z-50 bg-transparent backdrop-blur supports-[backdrop-filter]:bg-black/0">
            <div class="mx-auto w-full max-w-4xl px-6 lg:px-8 py-3">
                <nav class="grid grid-cols-3 items-center">
                    <div></div>

                    <div class="flex items-center justify-center gap-6 text-sm">
                        <Link href="/" class="px-1 py-1.5 leading-normal text-red-400 hover:opacity-80">Domov</Link>
                        <Link href="/bany" class="px-1 py-1.5 leading-normal text-[#EDEDEC] font-medium">Bany</Link>
                        <Link href="/staff" class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">Staff</Link>
                        <Link href="/discord" class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">Discord</Link>
                    </div>

                    <div class="flex items-center justify-end gap-4 text-sm">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="px-1 py-1.5 text-[#EDEDEC] hover:opacity-80">Dashboard</Link>
                        <template v-else>
                            <Link :href="route('login')" class="px-1 py-1.5 text-[#EDEDEC] hover:opacity-80">Přihlásit
                            se</Link>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Content -->
        <section
            class="relative z-10 w-full text-white flex items-start justify-center min-h-[calc(100vh-4.5rem)] lg:min-h-[calc(100vh-6rem)]">
            <div class="mx-auto w-full max-w-7xl px-4 lg:px-8 py-10">
                <!-- Badge + title -->
                <div class="flex justify-center mb-6">
                    <div
                        class="bg-blue-600/20 text-blue-400 px-4 py-2 rounded-full text-sm font-medium border border-blue-600/30">
                        NÁŠ TÝM
                    </div>
                </div>
                <h1 class="text-4xl lg:text-5xl font-bold text-center mb-3">
                    SERVER STAFF
                </h1>
                <p class="text-center text-white/70 mb-10 max-w-2xl mx-auto">
                    Náš odhodlaný tým pracuje 24/7, aby zajistil nejlepší herní zážitek a
                    bezpečné prostředí pro každého hráče.
                </p>

                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                    <div class="bg-neutral-900/70 border border-white/10 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <Users class="w-6 h-6 text-white/80" />
                        </div>
                        <div class="text-2xl font-bold text-white">{{ stats.members }}</div>
                        <div class="text-sm text-white/70">Členů týmu</div>
                    </div>
                    <div class="bg-neutral-900/70 border border-green-600/20 rounded-xl p-6 text-center">
                        <div
                            class="w-12 h-12 bg-green-600/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <Zap class="w-6 h-6 text-green-400" />
                        </div>
                        <div class="text-2xl font-bold text-green-400">
                            {{ stats.online }}
                        </div>
                        <div class="text-sm text-white/70">Nyní online</div>
                    </div>
                    <div class="bg-neutral-900/70 border border-pink-600/20 rounded-xl p-6 text-center">
                        <div
                            class="w-12 h-12 bg-pink-600/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <HeartHandshake class="w-6 h-6 text-pink-400" />
                        </div>
                        <div class="text-2xl font-bold text-pink-400">
                            {{ stats.helped.toLocaleString('cs-CZ') }}
                        </div>
                        <div class="text-sm text-white/70">
                            Hráči kterým náš A-Tým pomohl
                        </div>
                    </div>
                    <div class="bg-neutral-900/70 border border-yellow-600/20 rounded-xl p-6 text-center">
                        <div
                            class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <Medal class="w-6 h-6 text-yellow-400" />
                        </div>
                        <div class="text-2xl font-bold text-yellow-400">
                            {{ stats.avgYears }}y
                        </div>
                        <div class="text-sm text-white/70">Průměrná zkušenost</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-neutral-900/60 rounded-xl p-4 mb-8 border border-white/5 flex flex-wrap gap-2">
                    <button class="px-3 py-2 rounded-md text-sm font-medium transition-colors" :class="filters === 'ALL'
                        ? 'bg-white/10 text-white'
                        : 'bg-transparent text-white/80 hover:bg-white/10'
                        " @click="filters = 'ALL'">
                        Všichni
                    </button>
                    <button
                        v-for="r in ['OWNER', 'CO_OWNER', 'ADMIN', 'TEAM_LEADER', 'DEV', 'BUILDER', 'EVENTER', 'HELPER']"
                        :key="r"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors inline-flex items-center gap-1"
                        :class="filters === (r as Role)
                            ? 'bg-white/10 text-white'
                            : 'bg-transparent text-white/80 hover:bg-white/10'
                            " @click="filters = r as Role">
                        <template v-if="r === 'DEV'">
                            <Code2 class="w-4 h-4" />
                        </template>
                        <template v-else-if="r === 'BUILDER'">
                            <Wrench class="w-4 h-4" />
                        </template>
                        <template v-else-if="r === 'ADMIN'">
                            <ShieldHalf class="w-4 h-4" />
                        </template>
                        <template v-else-if="r === 'HELPER'">
                            <HelpCircle class="w-4 h-4" />
                        </template>
                        <template v-else-if="r === 'OWNER' || r === 'CO_OWNER'">
                            <Crown class="w-4 h-4" />
                        </template>
                        <template v-else-if="r === 'TEAM_LEADER'">
                            <Trophy class="w-4 h-4" />
                        </template>
                        <template v-else-if="r === 'EVENTER'">
                            <Calendar class="w-4 h-4" />
                        </template>
                        <template v-else>
                            <Star class="w-4 h-4" />
                        </template>
                        {{ r }}
                    </button>
                </div>

                <!-- Staff grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                    <div v-for="m in [...filteredStaff].sort((a, b) => roleOrder[a.role] - roleOrder[b.role])"
                        :key="m.id" class="rounded-2xl overflow-hidden border border-white/5 bg-neutral-900/60">
                        <!-- Card header -->
                        <div class="p-6" :class="`bg-${m.color ?? 'neutral'}-900/50`">
                            <div class="flex items-center gap-4">
                                <!-- MC-Heads avatar -->
                                <img :src="headRenderUrl(m.username, 96)" :alt="m.username" class="w-14 h-14 rounded-xl"
                                    @error="(e: any) => { e.target.onerror = null; e.target.src = avatarUrl(m.username, 96); }" />
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-semibold">{{ m.username }}</h3>
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full border"
                                            :class="{
                                                'border-rose-500/40 text-rose-300 bg-rose-500/10': m.role === 'OWNER',
                                                'border-fuchsia-500/40 text-fuchsia-300 bg-fuchsia-500/10': m.role === 'CO_OWNER',
                                                'border-amber-500/40 text-amber-300 bg-amber-500/10': m.role === 'ADMIN',
                                                'border-cyan-500/40 text-cyan-300 bg-cyan-500/10': m.role === 'TEAM_LEADER',
                                                'border-emerald-500/40 text-emerald-300 bg-emerald-500/10': m.role === 'DEV',
                                                'border-yellow-500/40 text-yellow-300 bg-yellow-500/10': m.role === 'BUILDER',
                                                'border-orange-500/40 text-orange-300 bg-orange-500/10': m.role === 'EVENTER',
                                                'border-violet-500/40 text-violet-300 bg-violet-500/10': m.role === 'HELPER'
                                            }">
                                            <Star class="w-3.5 h-3.5" />
                                            {{ roleLabel[m.role] }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-white/70 -mt-0.5">{{ m.title }}</div>
                                </div>

                                <span class="inline-flex items-center justify-center w-3.5 h-3.5 rounded-full"
                                    :class="m.online ? 'bg-green-400' : 'bg-gray-500/50'"
                                    :title="m.online ? 'Online' : 'Offline'">
                                    <Circle class="w-3 h-3 text-transparent" />
                                </span>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-6 pt-4">
                            <p class="text-sm text-white/80 mb-4">
                                {{ m.bio }}
                            </p>

                            <div class="grid grid-cols-3 gap-3 text-center">
                                <div class="bg-white/5 rounded-lg p-3 border border-white/5" title="Hráčům pomoženo">
                                    <div class="text-lg font-bold">
                                        {{ (m.helped ?? 0).toLocaleString('cs-CZ') }}
                                    </div>
                                    <div class="text-xs text-white/60">pomoc</div>
                                </div>
                                <div class="bg-white/5 rounded-lg p-3 border border-white/5" title="Zkušenosti">
                                    <div class="text-lg font-bold">{{ m.years ?? 0 }}y</div>
                                    <div class="text-xs text-white/60">zkušenost</div>
                                </div>
                                <div class="bg-white/5 rounded-lg p-3 border border-white/5"
                                    :title="m.online ? 'Online' : 'Offline'">
                                    <div class="text-lg font-bold"
                                        :class="m.online ? 'text-green-400' : 'text-white/80'">
                                        {{ m.online ? 'Online' : 'Offline' }}
                                    </div>
                                    <div class="text-xs text-white/60">status</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="filteredStaff.length === 0" class="px-6 py-12 text-center text-white/80">
                    Pro daný filtr nebyli nalezeni žádní členové týmu.
                </div>
            </div>
        </section>

        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>