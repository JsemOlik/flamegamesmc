<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Instagram, Youtube } from 'lucide-vue-next';
import { Button } from "@/components/ui/button"

const serverIp = 'mc.qpvp.pro';
const copied = ref(false);

// Discord counts
const discordOnline = ref<number | null>(null);
const discordMembers = ref<number | null>(null);
const discordError = ref<string | null>(null);

async function copyIp() {
    try {
        await navigator.clipboard.writeText(serverIp);
        copied.value = true;
        setTimeout(() => (copied.value = false), 1500);
    } catch (err) {
        console.error('Clipboard copy failed:', err);
        const textarea = document.createElement('textarea');
        textarea.value = serverIp;
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            copied.value = true;
            setTimeout(() => (copied.value = false), 1500);
        } finally {
            document.body.removeChild(textarea);
        }
    }
}


async function fetchDiscordCounts() {
    try {
        discordError.value = null;
        const res = await fetch(
            'https://discord.com/api/v10/invites/uS4V6Be8Vk?with_counts=true&with_expiration=true',
            {
                // Using GET; no auth needed for public invite counts
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            }
        );

        if (!res.ok) {
            throw new Error(`Discord API error ${res.status}`);
        }

        const data = await res.json();
        // These fields may be undefined if the invite is invalid or expired
        discordMembers.value = data?.approximate_member_count ?? null;
        discordOnline.value = data?.approximate_presence_count ?? null;
    } catch (e: any) {
        console.error('Failed to fetch Discord counts:', e);
        discordError.value = 'Nepodařilo se načíst čísla z Discordu.';
    }
}

onMounted(() => {
    fetchDiscordCounts();
    // Optional: refresh every 60s
    setInterval(fetchDiscordCounts, 60000);
});
</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <!-- Page wrapper -->
    <div class="relative flex min-h-screen flex-col items-center pt-20 lg:pt-24">
        <!-- Background layer (visible). Start with solid color to verify -->
        <!-- When you see it, swap background to the image by uncommenting the style line -->
        <div class="absolute inset-0 z-0 bg-black"
            :style="{ backgroundImage: `url('/header.png')`, backgroundSize: 'cover', backgroundPosition: 'center', opacity: 0.1 }"
            aria-hidden="true"></div>

        <!-- Fixed header -->
        <header
            class="fixed top-0 left-0 right-0 z-50 bg-transparent backdrop-blur supports-[backdrop-filter]:bg-black/0">
            <div class="mx-auto w-full max-w-4xl px-6 lg:px-8 py-3">
                <nav class="grid grid-cols-3 items-center">
                    <!-- Left spacer -->
                    <div></div>

                    <!-- Centered nav links (text only) -->
                    <div class="flex items-center justify-center gap-6 text-sm">
                        <Link :href="route('home')" class="px-1 py-1.5 leading-normal text-red-400 hover:opacity-80">
                        Home
                        </Link>
                        <Link :href="route('bany')" class="px-1 py-1.5 leading-normal text-[#EDEDEC] font-medium">
                        Bany
                        </Link>
                        <Link :href="route('staff')" class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">
                        Staff
                        </Link>
                        <Link href="/discord" class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80">
                        Discord
                        </Link>
                    </div>

                    <!-- Right-aligned auth buttons (text only) -->
                    <div class="flex items-center justify-end gap-4 text-sm">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80 dark:text-[#EDEDEC]">
                        Tickety
                        </Link>
                        <template v-else>
                            <Link :href="route('login')"
                                class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80 dark:text-[#EDEDEC]">
                            Přihlásit se
                            </Link>
                            <!--
          <Link
            :href="route('register')"
            class="px-1 py-1.5 leading-normal text-[#EDEDEC] hover:opacity-80 dark:text-[#EDEDEC]"
          >
            Register
          </Link>
          -->
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Hero (on top of background) -->
        <section
            class="relative z-10 w-full text-white flex items-center justify-center min-h-[calc(100vh-4.5rem)] lg:min-h-[calc(100vh-6rem)]">
            <div class="mx-auto max-w-5xl px-4 py-10 text-center">
                <h1
                    class="bg-red-500/40 w-fit mx-auto inline-block px-5 py-3 rounded-full text-base font-bold tracking-tight mb-12">
                    CZ/SK Komunita
                </h1>

                <h1 class="text-8xl md:text-9xl font-extrabold tracking-tight">
                    <span class="text-red-600">Q</span>PvP
                </h1>

                <p class="mt-3 text-2xl text-white/80">
                    <!-- Tu sa z nooba stáva legenda -->
                    Chill, PvP a mnohem víc!
                </p>

                <!-- <div class="mt-10 flex justify-center gap-2">
                    <Button variant="outline" size="icon">
                        <Instagram class="w-6 h-6" />
                    </Button>

                    <Button variant="outline" size="icon">
                        <Youtube class="w-4 h-4" />
                    </Button>
                </div> -->

                <div class="mt-16 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button @click="copyIp"
                        class="cursor-pointer p-5 rounded-xl border-2 border-red-200/15 bg-red-600/15 flex items-center gap-4 sm:min-w-[260px] text-left hover:bg-red-600/20 transition"
                        :aria-label="`Copy server IP ${serverIp}`">
                        <img src="/FlameGames.webp" class="w-12 h-12 shrink-0 rounded-lg" alt="Flame Games" />
                        <div class="flex flex-col">
                            <span class="text-xl font-bold">239 Hráčů</span>
                            <span class="text-sm opacity-80">
                                {{ copied ? 'Zkopírováno!' : 'Klikni pro IP!' }}
                            </span>
                        </div>
                    </button>

                    <a href="/discord" target="_blank" rel="noopener noreferrer"
                        class="p-5 rounded-xl border-2 bg-neutral-800/50 flex items-center gap-4 sm:min-w-[260px] text-left hover:bg-neutral-800/70 transition">
                        <img src="/discord.svg" class="w-12 h-12 shrink-0 rounded-lg" alt="Discord" />
                        <div class="flex flex-col">
                            <span class="text-xl font-bold">
                                <!-- Prefer online count; fallback to members; final fallback to placeholder -->
                                {{ discordOnline !== null
                                    ? `${discordOnline} Online`
                                    : (discordMembers !== null
                                        ? `${discordMembers} Members`
                                        : 'Načítám...') }}
                            </span>
                            <span class="text-sm opacity-80">
                                Připoj se na náš Discord!
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>