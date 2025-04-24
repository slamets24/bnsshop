<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const stats = ref([
    { name: 'Total Penjualan', value: 'Rp24.000.000', change: '+12%', changeType: 'positive' },
    { name: 'Pengguna Aktif', value: '1.234', change: '+8%', changeType: 'positive' },
    { name: 'Pesanan Tertunda', value: '45', change: '-2%', changeType: 'negative' },
    { name: 'Tingkat Konversi', value: '3,2%', change: '+0,5%', changeType: 'positive' },
]);
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="stat in stats" :key="stat.name"
                        class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ stat.name }}
                                    </div>
                                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ stat.value }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3 dark:bg-gray-700">
                            <div class="text-sm">
                                <span :class="{
                                    'text-green-600 dark:text-green-400': stat.changeType === 'positive',
                                    'text-red-600 dark:text-red-400': stat.changeType === 'negative'
                                }">
                                    {{ stat.change }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400"> dari bulan lalu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
