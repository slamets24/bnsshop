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

const recentActivity = ref([
    { id: 1, user: 'Budi Santoso', action: 'membuat pesanan', time: '2 menit yang lalu' },
    { id: 2, user: 'Siti Aminah', action: 'memperbarui profil', time: '5 menit yang lalu' },
    { id: 3, user: 'Ahmad Hidayat', action: 'menyelesaikan pembayaran', time: '10 menit yang lalu' },
    { id: 4, user: 'Dewi Putri', action: 'meminta pengembalian dana', time: '15 menit yang lalu' },
]);
</script>

<template>

    <Head title="Beranda" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Beranda
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Stats Cards -->
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

                <!-- Charts Section -->
                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Sales Chart -->
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Ringkasan Penjualan
                            </h3>
                            <div class="mt-4 h-64">
                                <!-- Placeholder for chart -->
                                <div class="flex h-full items-center justify-center text-gray-500 dark:text-gray-400">
                                    Grafik akan ditampilkan di sini
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Ringkasan Pendapatan
                            </h3>
                            <div class="mt-4 h-64">
                                <!-- Placeholder for chart -->
                                <div class="flex h-full items-center justify-center text-gray-500 dark:text-gray-400">
                                    Grafik akan ditampilkan di sini
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mt-8">
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Aktivitas Terbaru
                            </h3>
                            <div class="mt-4">
                                <div class="flow-root">
                                    <ul role="list" class="-mb-8">
                                        <li v-for="activity in recentActivity" :key="activity.id">
                                            <div class="relative pb-8">
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span
                                                            class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                            <span class="text-sm font-medium text-white">
                                                                {{ activity.user.charAt(0) }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                <span class="font-medium text-gray-900 dark:text-white">
                                                                    {{ activity.user }}
                                                                </span>
                                                                {{ activity.action }}
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                            <time>{{ activity.time }}</time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
