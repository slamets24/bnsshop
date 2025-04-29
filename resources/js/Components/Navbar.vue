<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const navigation = [
    { name: 'Beranda', href: '/' },
    { name: 'Produk', href: '/products' },
    { name: 'Kategori', href: '/categories' },
];

const mobileMenuOpen = ref(false);
</script>

<template>
    <header class="bg-white dark:bg-gray-800">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <Link href="/" class="-m-1.5 p-1.5">
                <span class="sr-only">KatonShop</span>
                <ApplicationLogo class="h-8 w-auto text-emerald-600 dark:text-emerald-400" />
                </Link>
            </div>
            <div class="flex lg:hidden">
                <button type="button" @click="mobileMenuOpen = true"
                    class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-200">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
            <div class="hidden lg:flex lg:gap-x-12">
                <Link v-for="item in navigation" :key="item.name" :href="item.href"
                    class="text-sm font-semibold leading-6 text-gray-900 hover:text-emerald-600 dark:text-white dark:hover:text-emerald-400">
                {{ item.name }}
                </Link>
            </div>
            <div class="hidden lg:flex lg:flex-1 lg:justify-end">
                <Link v-if="$page.props.canLogin" :href="route('login')"
                    class="text-sm font-semibold leading-6 text-gray-900 hover:text-emerald-600 dark:text-white dark:hover:text-emerald-400">
                Log in <span aria-hidden="true">&rarr;</span>
                </Link>
            </div>
        </nav>
        <!-- Mobile menu -->
        <div v-if="mobileMenuOpen" class="lg:hidden">
            <div class="fixed inset-0 z-10" />
            <div
                class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white dark:bg-gray-800 px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <Link href="/" class="-m-1.5 p-1.5">
                    <span class="sr-only">KatonShop</span>
                    <ApplicationLogo class="h-8 w-auto text-emerald-600 dark:text-emerald-400" />
                    </Link>
                    <button type="button" @click="mobileMenuOpen = false"
                        class="-m-2.5 rounded-md p-2.5 text-gray-700 dark:text-gray-200">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <Link v-for="item in navigation" :key="item.name" :href="item.href"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-gray-700">
                            {{ item.name }}
                            </Link>
                        </div>
                        <div class="py-6">
                            <Link v-if="$page.props.canLogin" :href="route('login')"
                                class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-gray-700">
                            Log in
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
