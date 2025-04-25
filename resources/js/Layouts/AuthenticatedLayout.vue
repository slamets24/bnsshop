<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const isDarkMode = ref(false);
const isSidebarCollapsed = ref(false);
const isMobileSidebarOpen = ref(false);

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }
};

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

// Initialize dark mode based on system preference
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDarkMode.value = true;
    document.documentElement.classList.add('dark');
} else {
    isDarkMode.value = false;
    document.documentElement.classList.remove('dark');
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Mobile Sidebar Toggle Button -->
        <button @click="toggleMobileSidebar"
            class="fixed bottom-4 right-4 z-50 md:hidden bg-indigo-600 text-white p-3 rounded-full shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Mobile Sidebar Overlay -->
        <div v-if="isMobileSidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
            @click="toggleMobileSidebar">
        </div>

        <!-- Sidebar -->
        <aside :class="{
            'w-64': !isSidebarCollapsed,
            'w-20': isSidebarCollapsed,
            'translate-x-0': isMobileSidebarOpen,
            '-translate-x-full': !isMobileSidebarOpen
        }"
            class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform md:translate-x-0">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                <Link :href="route('dashboard')" class="flex items-center">
                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                <span v-if="!isSidebarCollapsed" class="ml-3 text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Beranda
                </span>
                </Link>
                <button @click="toggleSidebar"
                    class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hidden md:block">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="isSidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="toggleMobileSidebar"
                    class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-2 py-4 space-y-1">
                <Link :href="route('dashboard')" :class="{
                    'bg-gray-100 dark:bg-gray-700': route().current('dashboard'),
                    'hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('dashboard')
                }" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span v-if="!isSidebarCollapsed" class="ml-3">Beranda</span>
                </Link>

                <Link :href="route('dashboard.products.index')" :class="{
                    'bg-gray-100 dark:bg-gray-700': route().current('dashboard.products.index'),
                    'hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('dashboard.products.index')
                }" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span v-if="!isSidebarCollapsed" class="ml-3">Produk</span>
                </Link>

                <Link :href="route('dashboard.transactions')" :class="{
                    'bg-gray-100 dark:bg-gray-700': route().current('dashboard.transactions'),
                    'hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('dashboard.transactions')
                }" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span v-if="!isSidebarCollapsed" class="ml-3">Transaksi</span>
                </Link>

                <Link :href="route('dashboard.categories.index')" :class="{
                    'bg-gray-100 dark:bg-gray-700': route().current('dashboard.categories.*'),
                    'hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('dashboard.categories.*')
                }" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span v-if="!isSidebarCollapsed" class="ml-3">Kategori</span>
                </Link>

                <Link :href="route('dashboard.shipments')" :class="{
                    'bg-gray-100 dark:bg-gray-700': route().current('dashboard.shipments'),
                    'hover:bg-gray-50 dark:hover:bg-gray-700': !route().current('dashboard.shipments')
                }" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                </svg>
                <span v-if="!isSidebarCollapsed" class="ml-3">Pengiriman</span>
                </Link>
            </nav>

            <!-- Bottom Section -->
            <div class="flex-shrink-0 p-4 border-t border-gray-200 dark:border-gray-700">
                <Link :href="route('logout')" method="post" as="button"
                    class="flex items-center w-full px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span v-if="!isSidebarCollapsed" class="ml-3">Keluar</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="{
            'md:ml-64': !isSidebarCollapsed,
            'md:ml-20': isSidebarCollapsed,
            'ml-0': true
        }" class="transition-all duration-300">
            <!-- Page Heading -->
            <header class="bg-white shadow dark:bg-gray-800" v-if="$slots.header">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <slot name="header" />
                        <div class="flex items-center space-x-4">
                            <!-- Dark Mode Toggle -->
                            <button @click="toggleDarkMode"
                                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <svg v-if="isDarkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </button>

                            <!-- User Menu -->
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        class="flex items-center text-sm font-medium text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <div>{{ $page.props.auth.user.name }}</div>
                                        <div class="ml-1">
                                            <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profil</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.router-link-active {
    @apply bg-gray-100 dark:bg-gray-700;
}

/* Add smooth transition for sidebar */
aside {
    transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
}
</style>
