<script setup>
import { Head } from '@inertiajs/vue3';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

defineProps({
    product: {
        type: Object,
        required: true
    }
});
</script>

<template>

    <Head :title="product.name + ' - KatonShop'" />

    <FrontendLayout>
        <div class="bg-white dark:bg-gray-800">
            <div
                class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:grid lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8">
                <!-- Product details -->
                <div class="lg:max-w-lg lg:self-end">
                    <div class="mt-4">
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                            {{ product.name }}
                        </h1>
                    </div>

                    <section aria-labelledby="information-heading" class="mt-4">
                        <h2 id="information-heading" class="sr-only">Product information</h2>

                        <div class="flex items-center">
                            <p class="text-lg text-gray-900 dark:text-white sm:text-xl">Rp {{ product.price }}</p>
                        </div>

                        <div class="mt-4 space-y-6">
                            <p class="text-base text-gray-500 dark:text-gray-400">{{ product.description }}</p>
                        </div>

                        <div class="mt-6 flex items-center">
                            <div class="flex items-center">
                                <p class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ product.category.name }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Product image -->
                <div class="mt-10 lg:col-start-2 lg:row-span-2 lg:mt-0 lg:self-center">
                    <div class="aspect-h-1 aspect-w-1 overflow-hidden rounded-lg">
                        <img :src="product.image" :alt="product.name" class="h-full w-full object-cover object-center">
                    </div>
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>
