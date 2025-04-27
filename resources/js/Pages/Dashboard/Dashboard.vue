<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Chart } from 'chart.js/auto';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    stats: Array,
    salesChart: Array,
    topProducts: Array,
    recentActivity: Array,
    orderStatus: Object
});

const loading = ref(true);
let salesChartInstance = null;
let orderStatusChartInstance = null;

// Refresh function
const refresh = () => {
    loading.value = true;
    router.reload({
        onFinish: () => {
            loading.value = false;
        }
    });
};

// Auto refresh setiap 5 menit
let refreshInterval;
onMounted(() => {
    loading.value = false;
    refreshInterval = setInterval(refresh, 300000);

    // Initialize charts
    initializeCharts();
});

onUnmounted(() => {
    clearInterval(refreshInterval);
    if (salesChartInstance) salesChartInstance.destroy();
    if (orderStatusChartInstance) orderStatusChartInstance.destroy();
});

const initializeCharts = () => {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        salesChartInstance = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: props.salesChart.map(item => item.date),
                datasets: [{
                    label: 'Penjualan',
                    data: props.salesChart.map(item => item.total),
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Order Status Chart
    const statusCtx = document.getElementById('orderStatusChart');
    if (statusCtx) {
        const statusLabels = {
            pending: 'Menunggu',
            paid: 'Dibayar',
            processing: 'Diproses',
            shipped: 'Dikirim',
            delivered: 'Diterima',
            completed: 'Selesai',
            cancelled: 'Dibatalkan',
            failed: 'Gagal'
        };

        const statusColors = {
            pending: '#FCD34D',
            paid: '#34D399',
            processing: '#60A5FA',
            shipped: '#818CF8',
            delivered: '#A78BFA',
            completed: '#34D399',
            cancelled: '#EF4444',
            failed: '#9CA3AF'
        };

        orderStatusChartInstance = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(props.orderStatus).map(status => statusLabels[status]),
                datasets: [{
                    data: Object.values(props.orderStatus),
                    backgroundColor: Object.keys(props.orderStatus).map(status => statusColors[status])
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
};
</script>

<template>

    <Head title="Beranda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Beranda
                </h2>
                <button @click="refresh"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Loading Skeleton -->
                <div v-if="loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="i in 4" :key="i"
                        class="animate-pulse overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                            <div class="mt-4 h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                            <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-1/4"></div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-if="stats && stats.length > 0" v-for="stat in stats" :key="stat.name"
                        class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800 flex flex-col h-[160px]">
                        <div class="p-5 flex-1">
                            <div class="flex items-start h-full">
                                <div class="flex-shrink-0 mr-4">
                                    <!-- Sales Icon -->
                                    <svg v-if="stat.name === 'Total Penjualan'"
                                        class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <!-- Orders Icon -->
                                    <svg v-else-if="stat.name === 'Total Pesanan'"
                                        class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <!-- Pending Orders Icon -->
                                    <svg v-else-if="stat.name === 'Pesanan Tertunda'"
                                        class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <!-- Conversion Rate Icon -->
                                    <svg v-else class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        {{ stat.name }}
                                    </div>
                                    <div v-if="stat.name === 'Total Pesanan'" class="flex flex-col space-y-1">
                                        <div class="text-xl font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ stat.value.split(',')[0].trim() }}
                                        </div>
                                        <div class="text-xl font-semibold text-purple-600 dark:text-purple-400">
                                            {{ stat.value.split(',')[1].trim() }}
                                        </div>
                                    </div>
                                    <div v-else-if="stat.name === 'Total Penjualan'"
                                        class="text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ stat.value }}
                                    </div>
                                    <div v-else class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ stat.value }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-2 dark:bg-gray-700 h-[40px] flex items-center">
                            <div class="text-sm flex items-center space-x-1">
                                <span :class="{
                                    'text-green-600 dark:text-green-400': stat.changeType === 'positive',
                                    'text-red-600 dark:text-red-400': stat.changeType === 'negative'
                                }">
                                    {{ stat.change }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">dari bulan lalu</span>
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
                                Ringkasan Penjualan (7 Hari Terakhir)
                            </h3>
                            <div v-if="salesChart && salesChart.length > 0" class="mt-4 h-64">
                                <canvas id="salesChart"></canvas>
                            </div>
                            <div v-else class="mt-4 h-64 flex items-center justify-center">
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada data penjualan tersedia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Chart -->
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Distribusi Status Pesanan
                            </h3>
                            <div v-if="orderStatus && Object.keys(orderStatus).length > 0" class="mt-4 h-64">
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                            <div v-else class="mt-4 h-64 flex items-center justify-center">
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada data status pesanan tersedia</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products and Recent Activity -->
                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Top Products -->
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Produk Terlaris Bulan Ini
                            </h3>
                            <div v-if="topProducts && topProducts.length > 0" class="mt-4">
                                <div class="flow-root">
                                    <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                                        <li v-for="product in topProducts" :key="product.id" class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="min-w-0 flex-1">
                                                    <p
                                                        class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ product.name }}
                                                    </p>
                                                    <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                                                        Terjual: {{ product.total_sold }} unit
                                                    </p>
                                                </div>
                                                <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                                    Rp{{ new Intl.NumberFormat('id-ID').format(product.price) }}
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div v-else class="mt-4 py-4 text-center">
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada produk terlaris tersedia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="p-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Aktivitas Terbaru
                            </h3>
                            <div v-if="recentActivity && recentActivity.length > 0" class="mt-4">
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
                            <div v-else class="mt-4 py-4 text-center">
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada aktivitas terbaru tersedia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
