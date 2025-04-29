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

    <Head title="Kategori - KatonShop" />

    <FrontendLayout>
        <div class="bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl py-16 sm:py-24 lg:max-w-none lg:py-32">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori</h2>

                    <div class="mt-6 space-y-12 lg:grid lg:grid-cols-3 lg:gap-x-6 lg:space-y-0">
                        <div v-for="category in categories" :key="category.id" class="group relative">
                            <div
                                class="relative h-80 w-full overflow-hidden rounded-lg bg-white dark:bg-gray-700 sm:aspect-h-1 sm:aspect-w-2 lg:aspect-h-1 lg:aspect-w-1 group-hover:opacity-75 sm:h-64">
                                <img :src="category.image" :alt="category.name"
                                    class="h-full w-full object-cover object-center">
                            </div>
                            <h3 class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                                <Link :href="route('categories.show', category.id)">
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
