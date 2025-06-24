<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];
</script>

<template>

    <Head title="Statistiky" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-3 rounded-md w-100% h-full min-h-[60vh] flex flex-col items-center justify-center bg-neutral-900 shadow-lg overflow-hidden">
            <h1 class="text-3xl font-bold text-white mb-2">
                Pracujeme na tom ;)
            </h1>
            <p class="text-neutral-400 text-lg">Statistiky budou brzy dostupné.</p>
        </div>
    </AppLayout>
</template>

<style scoped>
body {
  overflow: hidden;
}
</style>
