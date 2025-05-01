<script setup>
import { Head } from '@inertiajs/vue3';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    categories: {
        type: Array,
        required: true
    }
});
</script>

<template>

    <Head title="BNS | Kategori" />

    <FrontendLayout>
        <div class="bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl py-16 sm:py-24 lg:max-w-none lg:py-32">
                    <nav class="flex mb-8" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <Link :href="route('welcome')"
                                    class="text-gray-500 dark:text-gray-400 hover:text-emerald-600">
                                Beranda
                                </Link>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 9 4-4-4-4" />
                                    </svg>
                                    <span class="text-gray-500 dark:text-gray-400">Kategori</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Semua Kategori</h2>

                    <div class="mt-6 space-y-12 lg:grid lg:grid-cols-3 lg:gap-x-6 lg:space-y-0">
                        <div v-for="category in categories" :key="category.id" class="group relative">
                            <div
                                class="relative h-80 w-full overflow-hidden rounded-lg bg-white dark:bg-gray-700 sm:aspect-h-1 sm:aspect-w-2 lg:aspect-h-1 lg:aspect-w-1 group-hover:opacity-75 sm:h-64">
                                <img :src="category.image" :alt="category.name"
                                    class="h-full w-full object-cover object-center">
                            </div>
                            <h3 class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                                <Link :href="route('categories.show', { category: category.name })">
                                <span class="absolute inset-0"></span>
                                {{ category.name }}
                                </Link>
                            </h3>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ category.products_count
                            }}
                                Produk</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>
