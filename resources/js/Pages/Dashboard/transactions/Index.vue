<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Modal from '@/Components/Modal.vue';
import SlideNotification from '@/Components/SlideNotification.vue';

const props = defineProps({
    transactions: Object,
    filters: Object,
    message: String
});

const search = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || '');
const startDate = ref(props.filters?.start_date || '');
const endDate = ref(props.filters?.end_date || '');
const showDeleteModal = ref(false);
const showViewModal = ref(false);
const transactionToDelete = ref(null);
const selectedTransaction = ref(null);
const notification = ref({
    show: false,
    type: 'success',
    message: ''
});

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

const orderTypeLabels = {
    online: 'Online',
    offline: 'Offline'
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    shipped: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
    delivered: 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    failed: 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300'
};

const orderTypeColors = {
    online: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    offline: 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300'
};

const statusOptions = [
    { value: '', label: 'Semua Status' },
    { value: 'pending', label: 'Menunggu' },
    { value: 'paid', label: 'Dibayar' },
    { value: 'processing', label: 'Diproses' },
    { value: 'shipped', label: 'Dikirim' },
    { value: 'delivered', label: 'Diterima' },
    { value: 'completed', label: 'Selesai' },
    { value: 'cancelled', label: 'Dibatalkan' },
    { value: 'failed', label: 'Gagal' }
];

const deleteTransaction = (transaction) => {
    transactionToDelete.value = transaction;
    showDeleteModal.value = true;
};

const viewTransaction = (transaction) => {
    selectedTransaction.value = transaction;
    showViewModal.value = true;
};

const confirmDelete = () => {
    router.delete(route('dashboard.transactions.destroy', transactionToDelete.value.id), {
        onSuccess: () => {
            showNotification('success', 'Transaksi berhasil dihapus');
        },
        onError: () => {
            showNotification('error', 'Gagal menghapus transaksi');
        }
    });
    showDeleteModal.value = false;
    transactionToDelete.value = null;
};

const showNotification = (type, message) => {
    notification.value = {
        show: true,
        type,
        message
    };
};

// Show success notification if there's a message prop
if (props.message) {
    showNotification('success', props.message);
}

const formatDateForServer = (date) => {
    if (!date) return null;
    return date;
};

// Update watch untuk memformat tanggal
watch([search, selectedStatus, startDate, endDate], debounce(([searchVal, statusVal, startVal, endVal]) => {
    router.get(route('dashboard.transactions.index'),
        {
            search: searchVal || null,
            status: statusVal || null,
            start_date: formatDateForServer(startVal),
            end_date: formatDateForServer(endVal)
        },
        { preserveState: true, preserveScroll: true }
    );
}, 300));

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>

    <Head title="Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Transaksi
            </h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link :href="route('dashboard')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            Dashboard
                            </Link>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Transaksi
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-6 flex flex-col md:flex-row gap-4">
                            <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                                <!-- Search Input -->
                                <div class="relative">
                                    <input type="text" v-model="search" placeholder="Cari transaksi..."
                                        class="w-full px-4 py-2 pr-8 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:border-transparent" />
                                    <svg class="w-5 h-5 absolute right-2 top-2.5 text-gray-500 dark:text-gray-400"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <!-- Status Dropdown -->
                                <div>
                                    <select v-model="selectedStatus"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:border-transparent">
                                        <option v-for="option in statusOptions" :key="option.value"
                                            :value="option.value">
                                            {{ option.label }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Date Range -->
                                <div>
                                    <input type="date" v-model="startDate"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:border-transparent"
                                        :max="endDate || undefined" placeholder="Tanggal Mulai" />
                                </div>

                                <div>
                                    <input type="date" v-model="endDate"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:border-transparent"
                                        :min="startDate || undefined" placeholder="Tanggal Akhir" />
                                </div>
                            </div>

                            <div class="flex md:items-start">
                                <Link :href="route('dashboard.transactions.create')"
                                    class="w-full md:w-auto px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 whitespace-nowrap text-center">
                                Tambah Transaksi Offline
                                </Link>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            No
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Kode Transaksi
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tipe Order
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tanggal
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-if="transactions && transactions.data && transactions.data.length > 0"
                                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="(transaction, index) in transactions.data" :key="transaction.id">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ transactions.from + index }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ transaction.transaction_code }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatPrice(transaction.total) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                statusColors[transaction.status]
                                            ]">
                                                {{ statusLabels[transaction.status] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                orderTypeColors[transaction.order_type]
                                            ]">
                                                {{ orderTypeLabels[transaction.order_type] }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatDate(transaction.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                <button @click="viewTransaction(transaction)"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                                    title="Lihat Detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <Link
                                                    :href="route('dashboard.transactions.edit', transaction.transaction_code)"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                    title="Edit Transaksi">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                </Link>
                                                <button v-if="['cancelled', 'failed'].includes(transaction.status)"
                                                    @click="deleteTransaction(transaction)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    title="Hapus Transaksi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else class="bg-white dark:bg-gray-800">
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 mb-4 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                                <p class="text-lg font-medium">Tidak ada transaksi tersedia</p>
                                                <p class="text-sm mt-1">Coba tambahkan transaksi baru atau ubah filter
                                                    pencarian
                                                    Anda</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            <nav v-if="transactions && transactions.data && transactions.data.length > 0"
                                class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link v-if="transactions.current_page > 1" :href="transactions.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Sebelumnya
                                    </Link>
                                    <Link v-if="transactions.current_page < transactions.last_page"
                                        :href="transactions.next_page_url"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Selanjutnya
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            Menampilkan
                                            <span class="font-medium">{{ transactions.from }}</span>
                                            sampai
                                            <span class="font-medium">{{ transactions.to }}</span>
                                            dari
                                            <span class="font-medium">{{ transactions.total }}</span>
                                            data
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                            aria-label="Pagination">
                                            <template v-for="(link, index) in transactions.links" :key="index">
                                                <template
                                                    v-if="!(transactions.current_page === 1 && link.label === '&laquo; Previous')">
                                                    <template
                                                        v-if="!(transactions.current_page === transactions.last_page && link.label === 'Next &raquo;')">
                                                        <div v-if="link.url === null" v-html="link.label"
                                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800">
                                                        </div>
                                                        <Link v-else :href="link.url"
                                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                                                            :class="{ 'bg-indigo-50 dark:bg-indigo-900': link.active }">
                                                        <span
                                                            v-html="link.label.replace('Previous', 'Sebelumnya').replace('Next', 'Selanjutnya')"></span>
                                                        </Link>
                                                    </template>
                                                </template>
                                            </template>
                                        </nav>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal :show="showDeleteModal" title="Hapus Transaksi"
            :message="'Apakah Anda yakin ingin menghapus transaksi ' + (transactionToDelete?.transaction_code || '') + '?'"
            @close="showDeleteModal = false" @confirm="confirmDelete" />

        <!-- View Transaction Modal -->
        <Modal :show="showViewModal" @close="showViewModal = false" maxWidth="2xl">
            <div class="p-6 bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Detail Transaksi
                    </h3>
                    <button @click="showViewModal = false"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div v-if="selectedTransaction" class="space-y-4">
                    <!-- Transaction Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Transaksi</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ selectedTransaction.transaction_code }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ formatPrice(selectedTransaction.total) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                            <p class="mt-1">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    statusColors[selectedTransaction.status]
                                ]">
                                    {{ statusLabels[selectedTransaction.status] }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipe Order</p>
                            <p class="mt-1">
                                <span :class="[
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    orderTypeColors[selectedTransaction.order_type]
                                ]">
                                    {{ orderTypeLabels[selectedTransaction.order_type] }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Transaction Items -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Item Transaksi</h4>
                        <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Produk
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Harga
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Jumlah
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="item in selectedTransaction.items" :key="item.id">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ item.product.name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatPrice(item.product.price) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ item.quantity }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatPrice(item.product.price * item.quantity) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Informasi Tambahan</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ selectedTransaction.creator?.name ||
                                            (selectedTransaction.order_type === 'online' ?
                                                (selectedTransaction.shipping_address?.recipient_name + ' (Customer)') :
                                                '-') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Dibuat</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ formatDate(selectedTransaction.created_at) }}
                                    </p>
                                </div>
                                <div v-if="selectedTransaction.paid_at">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pembayaran
                                    </p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ formatDate(selectedTransaction.paid_at) }}
                                    </p>
                                </div>
                                <div v-if="selectedTransaction.payment_method">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Metode Pembayaran
                                    </p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ selectedTransaction.payment_method }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4" v-if="selectedTransaction.note">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ selectedTransaction.note }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <SlideNotification :show="notification?.show || false" :type="notification?.type || 'success'"
            :message="notification?.message || ''" @close="notification.show = false" />
    </AuthenticatedLayout>
</template>
