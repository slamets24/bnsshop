<script setup>
import { Head, Link } from '@inertiajs/vue3';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    category: {
        type: Object,
        required: true
    },
    products: {
        type: Object,
        required: true
    }
});
</script>

<template>

    <Head :title="category.name + ' | BNS'" />

    <FrontendLayout>
        <div class="bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="flex mb-8" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link :href="route('welcome')"
                                class="text-gray-500 dark:text-gray-400 hover:text-emerald-600">
                            Beranda
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 9 4-4-4-4" />
                                </svg>
                                <Link :href="route('categories')"
                                    class="text-gray-500 dark:text-gray-400 hover:text-emerald-600">
                                Kategori
                                </Link>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 9 4-4-4-4" />
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">{{ category.name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ category.name }}
                </h2>

                <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                    <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                </div>

                <div class="mt-6">
                    <Pagination :links="products.links" />
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>
