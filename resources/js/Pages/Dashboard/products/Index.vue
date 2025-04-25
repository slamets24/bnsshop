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
    products: Object,
    filters: Object,
    message: String
});

const search = ref(props.filters?.search || '');
const showDeleteModal = ref(false);
const showViewModal = ref(false);
const productToDelete = ref(null);
const selectedProduct = ref(null);
const activeImageIndex = ref(0);
const notification = ref({
    show: false,
    type: 'success',
    message: ''
});

const deleteProduct = (product) => {
    productToDelete.value = product;
    showDeleteModal.value = true;
};

const viewProduct = (product) => {
    selectedProduct.value = product;
    showViewModal.value = true;
};

const confirmDelete = () => {
    router.delete(route('dashboard.products.destroy', productToDelete.value.id), {
        onSuccess: () => {
            showNotification('success', 'Produk berhasil dihapus');
        },
        onError: () => {
            showNotification('error', 'Gagal menghapus produk');
        }
    });
    showDeleteModal.value = false;
    productToDelete.value = null;
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

const changeActiveImage = (index) => {
    activeImageIndex.value = index;
};

const closeViewModal = () => {
    showViewModal.value = false;
    activeImageIndex.value = 0;
};

watch(search, debounce((value) => {
    router.get(route('dashboard.products.index'),
        { search: value },
        { preserveState: true, preserveScroll: true }
    );
}, 300));

// Add price formatter function in script setup
const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};
</script>

<template>

    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Produk
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
                                    Produk
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-6 flex justify-between items-center">
                            <div class="relative">
                                <input type="text" v-model="search" placeholder="Cari produk..."
                                    class="px-4 py-2 pr-8 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:border-transparent" />
                                <svg class="w-5 h-5 absolute right-2 top-2.5 text-gray-500 dark:text-gray-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <Link :href="route('dashboard.products.create')"
                                class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Tambah Produk
                            </Link>
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
                                            Nama Produk
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            SKU
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Kategori
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Harga
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Stok
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tanggal Dibuat
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="(product, index) in products.data" :key="product.id">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ products.from + index }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ product.name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ product.sku }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ product.category.name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatPrice(product.price) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ product.stock }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ new Date(product.created_at).toLocaleDateString('id-ID') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                <button @click="viewProduct(product)"
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
                                                <Link :href="route('dashboard.products.edit', product.id)"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                    title="Edit Produk">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                </Link>
                                                <button @click="deleteProduct(product)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    title="Hapus Produk">
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
                            </table>
                        </div>

                        <div class="mt-4">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link v-if="products.current_page > 1" :href="products.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Sebelumnya
                                    </Link>
                                    <Link v-if="products.current_page < products.last_page"
                                        :href="products.next_page_url"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Selanjutnya
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            Menampilkan
                                            <span class="font-medium">{{ products.from }}</span>
                                            sampai
                                            <span class="font-medium">{{ products.to }}</span>
                                            dari
                                            <span class="font-medium">{{ products.total }}</span>
                                            data
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                            aria-label="Pagination">
                                            <template v-for="(link, index) in products.links" :key="index">
                                                <!-- Skip rendering Previous when on first page -->
                                                <template
                                                    v-if="!(products.current_page === 1 && link.label === '&laquo; Previous')">
                                                    <!-- Skip rendering Next when on last page -->
                                                    <template
                                                        v-if="!(products.current_page === products.last_page && link.label === 'Next &raquo;')">
                                                        <div v-if="link.url === null" v-html="link.label"
                                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800">
                                                        </div>
                                                        <Link v-else :href="link.url"
                                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                                                            :class="{ 'bg-indigo-50 dark:bg-indigo-900': link.active }">
                                                        <span
                                                            v-html="link.label.replace('Previous', 'Sebelumnya').replace('Selanjutnya')"></span>
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

        <ConfirmationModal :show="showDeleteModal" title="Hapus Produk"
            :message="'Apakah Anda yakin ingin menghapus produk ' + (productToDelete?.name || '') + '?'"
            @close="showDeleteModal = false" @confirm="confirmDelete" class="bg-white dark:bg-gray-800" />

        <Modal :show="showViewModal" @close="closeViewModal" maxWidth="2xl">
            <div class="p-4 sm:p-6 bg-white dark:bg-[#1e1e1e]">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Detail Produk
                    </h2>
                    <button @click="closeViewModal"
                        class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div v-if="selectedProduct" class="space-y-4 sm:space-y-8">
                    <!-- Product Images -->
                    <div v-if="selectedProduct.images && selectedProduct.images.length > 0" class="mb-4 sm:mb-6">
                        <div
                            class="relative h-48 sm:h-64 md:h-96 bg-gray-100 dark:bg-[#252525] rounded-lg overflow-hidden">
                            <img :src="selectedProduct.images[activeImageIndex].full_url" :alt="selectedProduct.name"
                                class="w-full h-full object-contain">
                        </div>
                        <!-- Thumbnail Gallery -->
                        <div v-if="selectedProduct.images.length > 1"
                            class="mt-2 sm:mt-4 grid grid-cols-4 sm:grid-cols-6 gap-2">
                            <div v-for="(image, index) in selectedProduct.images" :key="index"
                                @click="changeActiveImage(index)"
                                class="relative h-14 sm:h-16 bg-gray-100 dark:bg-[#252525] rounded-md overflow-hidden cursor-pointer hover:opacity-75 transition"
                                :class="{ 'ring-2 ring-indigo-500 dark:ring-indigo-400': activeImageIndex === index }">
                                <img :src="image.full_url" :alt="'${selectedProduct.name} image ' + (index + 1)"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8">
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Nama Produk -->
                            <div class="bg-gray-50 dark:bg-[#252525] rounded-lg p-4 sm:p-6">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 sm:mb-2">Nama
                                    Produk</h3>
                                <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-300">{{
                                    selectedProduct.name }}</p>
                            </div>

                            <!-- SKU -->
                            <div class="bg-gray-50 dark:bg-[#252525] rounded-lg p-4 sm:p-6">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 sm:mb-2">SKU</h3>
                                <p class="text-base sm:text-lg font-mono text-gray-900 dark:text-gray-300">{{
                                    selectedProduct.sku }}</p>
                            </div>

                            <!-- Kategori -->
                            <div class="bg-gray-50 dark:bg-[#252525] rounded-lg p-4 sm:p-6">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 sm:mb-2">Kategori
                                </h3>
                                <div
                                    class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ selectedProduct.category.name }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 sm:space-y-6">
                            <!-- Harga -->
                            <div class="bg-gray-50 dark:bg-[#252525] rounded-lg p-4 sm:p-6">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 sm:mb-2">Harga</h3>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-300">
                                    {{ formatPrice(selectedProduct.price) }}
                                </p>
                            </div>

                            <!-- Stok -->
                            <div class="bg-gray-50 dark:bg-[#252525] rounded-lg p-4 sm:p-6">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 sm:mb-2">Stok</h3>
                                <div class="flex items-center space-x-2 sm:space-x-3">
                                    <p class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-300">
                                        {{ selectedProduct.stock }}
                                    </p>
                                    <span
                                        class="px-2 sm:px-3 py-0.5 sm:py-1 text-xs sm:text-sm font-medium rounded-full"
                                        :class="{
                                            'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300': selectedProduct.stock === 0,
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300': selectedProduct.stock > 0 && selectedProduct.stock <= 10,
                                            'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': selectedProduct.stock > 10
                                        }">
                                        {{ selectedProduct.stock === 0 ? 'Habis' : selectedProduct.stock <= 10
                                            ? 'Hampir Habis' : 'Tersedia' }} </span>
                                </div>
                            </div>

                            <!-- Created At -->
                            <div class="bg-gray-50 dark:bg-[#252525] rounded-lg p-4 sm:p-6">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 sm:mb-2">Tanggal
                                    Dibuat
                                </h3>
                                <p class="text-sm sm:text-base text-gray-900 dark:text-gray-300">
                                    {{ new Date(selectedProduct.created_at).toLocaleDateString('id-ID', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Product Links -->
                    <div class="bg-gray-50 dark:bg-[#252525] rounded-lg p-4 sm:p-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 sm:mb-3">Tautan Produk</h3>
                        <div class="flex flex-wrap gap-4">
                            <a v-if="selectedProduct.links?.shopee_url" :href="selectedProduct.links.shopee_url"
                                target="_blank"
                                class="flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M21.3,11.9c-0.4-3.2-2.7-5.8-5.8-6.5c-0.2-0.1-0.4,0.1-0.4,0.3c0,0.1,0,0.2,0.1,0.3c0.6,0.9,0.9,1.9,0.9,3c0,3-2.4,5.4-5.4,5.4c-3,0-5.4-2.4-5.4-5.4c0-1.1,0.3-2.1,0.9-3c0.1-0.1,0.1-0.2,0.1-0.3c0-0.2-0.2-0.4-0.4-0.3c-3.1,0.8-5.4,3.3-5.8,6.5c-0.1,0.5-0.1,1-0.1,1.5c0,4.4,3.6,8,8,8s8-3.6,8-8C21.4,12.9,21.4,12.4,21.3,11.9z" />
                                    <path
                                        d="M12,11.4c2,0,3.5-1.6,3.5-3.5c0-2-1.6-3.5-3.5-3.5S8.5,5.9,8.5,7.9C8.5,9.8,10,11.4,12,11.4z" />
                                </svg>
                                <span class="font-medium">Shopee</span>
                            </a>
                            <a v-if="selectedProduct.links?.tokopedia_url" :href="selectedProduct.links.tokopedia_url"
                                target="_blank"
                                class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12,2L4,6v12l8,4l8-4V6L12,2z M12,4.618l5.333,2.667L12,9.951L6.667,7.285L12,4.618z M5.333,7.619l6.667,3.333v7.619 L5.333,15.238V7.619z M13.333,18.571v-7.619l6.667-3.333v7.619L13.333,18.571z" />
                                </svg>
                                <span class="font-medium">Tokopedia</span>
                            </a>
                            <a v-if="selectedProduct.links?.whatsapp_number"
                                :href="'https://wa.me/' + selectedProduct.links.whatsapp_number.replace(/\D/g, '')"
                                target="_blank"
                                class="flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12,2C6.5,2,2,6.5,2,12c0,2,0.6,3.9,1.6,5.4L2,22l4.6-1.6c1.5,1,3.4,1.6,5.4,1.6c5.5,0,10-4.5,10-10S17.5,2,12,2z M12,20.5c-1.8,0-3.5-0.6-4.9-1.6l-0.3-0.2l-3.4,1.2l1.2-3.4L4.4,16c-1-1.4-1.6-3.1-1.6-4.9C2.8,7,7,2.8,12,2.8s9.2,4.2,9.2,9.2S17,20.5,12,20.5z M16.2,14.2c-0.3-0.1-1.6-0.8-1.9-0.9c-0.3-0.1-0.5-0.1-0.7,0.1c-0.2,0.3-0.8,0.9-0.9,1.1c-0.2,0.2-0.3,0.2-0.6,0.1c-0.3-0.1-1.2-0.4-2.3-1.4c-0.9-0.8-1.4-1.7-1.6-2c-0.2-0.3,0-0.4,0.1-0.6c0.1-0.1,0.3-0.3,0.4-0.5c0.1-0.2,0.2-0.3,0.3-0.5c0.1-0.2,0-0.4,0-0.5c0-0.1-0.7-1.7-1-2.3C8,6.9,7.7,7,7.5,7C7.3,7,7.1,7,6.9,7C6.7,7,6.3,7.1,6,7.4C5.7,7.7,5,8.4,5,9.9c0,1.5,1.1,3,1.3,3.2c0.2,0.2,2.1,3.2,5.1,4.5c3,1.2,3,0.8,3.5,0.8c0.5-0.1,1.6-0.7,1.9-1.3c0.2-0.6,0.2-1.2,0.2-1.3C16.8,14.5,16.5,14.4,16.2,14.2z" />
                                </svg>
                                <span class="font-medium">WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <!-- Deskripsi - Full width -->
                    <div class="bg-gray-50 dark:bg-[#252525] rounded-lg p-4 sm:p-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 sm:mb-3">Deskripsi</h3>
                        <p
                            class="text-sm sm:text-base text-gray-900 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                            {{ selectedProduct.description }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 sm:mt-8 flex justify-end space-x-3">
                    <button type="button" @click="closeViewModal"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm bg-gray-100 dark:bg-[#323232] text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-[#3a3a3a] focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-600">
                        Tutup
                    </button>
                </div>
            </div>
        </Modal>

        <SlideNotification :show="notification.show" :type="notification.type" :message="notification.message"
            @close="notification.show = false" />
    </AuthenticatedLayout>
</template>

<style>
/* Add tooltip styles */
[title] {
    position: relative;
    cursor: pointer;
}

[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: -25px;
    left: 50%;
    transform: translateX(-50%);
    padding: 4px 8px;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    font-size: 12px;
    border-radius: 4px;
    white-space: nowrap;
    z-index: 10;
}
</style>
