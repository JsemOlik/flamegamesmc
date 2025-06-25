<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Ticket, User, Server, Ellipsis, ChartLine } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Tickety',
        href: '/tickets',
        icon: Ticket,
    },
    {
        title: 'Uživatelé',
        href: '/users',
        icon: User,
    },
    {
        title: 'Statistiky',
        href: '/statistic',
        icon: ChartLine,
    },
    {
        title: 'Status',
        href: '/status',
        icon: Server,
    },

    {
        title: 'Ostatní',
        href: '/others',
        icon: Ellipsis,
    },
];

const page = usePage();
const userRole = page.props.auth?.user?.role;

const mainNavItems = userRole === 'player'
    ? allNavItems.filter(item => item.title === 'Tickety')
    : allNavItems;

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    {
        title: 'Github Repo',
        href: 'https://github.com/jsemolik/vue-starter',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
