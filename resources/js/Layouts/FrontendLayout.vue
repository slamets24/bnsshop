<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const isDark = ref(false);
const showingNavigationDropdown = ref(false);
const { auth } = usePage().props;

const currentRoute = computed(() => route().current());

onMounted(() => {
    // Check if user has dark mode preference
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link :href="route('welcome')">
                        <img src="/img/logonew.png" alt="BNS Hijab" class="h-12 w-auto" />
                        </Link>
                    </div>

                    <div class="flex items-center">
                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:flex">
                            <Link :href="route('welcome')" :class="[
                                'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5',
                                currentRoute === 'welcome' ? 'text-gray-900 dark:text-gray-100 border-b-2 border-emerald-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                            ]">
                            Beranda
                            </Link>
                            <Link :href="route('products')" :class="[
                                'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5',
                                currentRoute === 'products' ? 'text-gray-900 dark:text-gray-100 border-b-2 border-emerald-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                            ]">
                            Produk
                            </Link>
                            <Link :href="route('categories')" :class="[
                                'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5',
                                currentRoute === 'categories' ? 'text-gray-900 dark:text-gray-100 border-b-2 border-emerald-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                            ]">
                            Kategori
                            </Link>
                        </div>

                        <!-- Admin Profile Dropdown -->
                        <div v-if="auth.user" class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                            <div>{{ auth.user.name }}</div>

                                            <div class="ml-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('dashboard')">
                                            Dashboard
                                        </DropdownLink>
                                        <form method="POST" @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <Link :href="route('welcome')" :class="[
                        'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out',
                        currentRoute === 'welcome' ? 'border-emerald-500 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                    ]">
                    Beranda
                    </Link>
                    <Link :href="route('products')" :class="[
                        'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out',
                        currentRoute === 'products' ? 'border-emerald-500 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                    ]">
                    Produk
                    </Link>
                    <Link :href="route('categories')" :class="[
                        'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out',
                        currentRoute === 'categories' ? 'border-emerald-500 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                    ]">
                    Kategori
                    </Link>
                </div>

                <!-- Responsive Settings Options -->
                <div v-if="auth.user" class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                            {{ auth.user.name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">{{ auth.user.email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <Link :href="route('dashboard')"
                            class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
                        Dashboard
                        </Link>
                        <form method="POST" @submit.prevent="logout">
                            <button type="submit"
                                class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode"
            class="fixed bottom-4 right-4 p-2 rounded-full bg-emerald-600 text-white dark:bg-emerald-500">
            <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <!-- Page Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="w-full bg-gray-800 dark:bg-gray-900 py-6">
            <div class="flex flex-col items-center px-3 py-5 md:flex-row">
                <!-- sisi kiri -->
                <div class="mx-auto text-center lg:w-1/2 md:mx-0">
                    <Link href="/" class="flex items-center justify-center font-medium text-gray-900 title-font">
                    <img class="w-16 md:w-20" src="/img/logonew.png" alt="">
                    </Link>
                    <p class="mt-2 text-sm text-white dark:text-gray-300">BNS Hijab Official Store</p>
                    <div class="mt-4">
                        <span class="inline-flex justify-center mt-2 sm:ml-auto sm:mt-0 sm:justify-start">
                            <a href="https://www.tokopedia.com/sampurnasnackofficial?source=universe&st=product"
                                class="ml-2 md:ml-3 text-white cursor-pointer hover:text-emerald-400">
                                <img fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    class="w-4 h-4 md:w-5 md:h-5" src="/img/tokoped.png" alt="">
                            </a>
                            <a href="https://www.instagram.com/bns_hijab/"
                                class="ml-2 md:ml-3 text-white cursor-pointer hover:text-emerald-400">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" class="w-4 h-4 md:w-5 md:h-5" viewBox="0 0 24 24">
                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5">
                                    </rect>
                                    <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
                                </svg>
                            </a>
                        </span>
                    </div>
                </div>
                <!-- akhir sisi kiri -->
                <!-- sisi kanan -->
                <div class="flex flex-wrap flex-grow mt-10 -mb-10 text-center md:text-right md:pl-20 md:mt-0">
                    <div class="w-full px-4 md:w-1/2">
                        <nav class="mb-10 list-none text-sm md:text-md">
                            <li class="mt-2 md:mt-3">
                                <Link href="/" class="text-white cursor-pointer hover:text-emerald-400">Home</Link>
                            </li>
                            <li class="mt-2 md:mt-3">
                                <Link href="/products" class="text-white cursor-pointer hover:text-emerald-400">Toko
                                </Link>
                            </li>
                            <li class="mt-2 md:mt-3">
                                <Link href="/about" class="text-white cursor-pointer hover:text-emerald-400">Tentang
                                Kami</Link>
                            </li>
                        </nav>
                    </div>
                </div>
                <!-- akhir sisi kanan -->
            </div>
            <div class="container mx-auto px-4 md:px-6 text-center text-white dark:text-gray-300">
                <p class="text-xs md:text-sm">&copy; {{ new Date().getFullYear() }} BNS. Created by Kulazutto.</p>
            </div>
        </footer>
    </div>
</template>
