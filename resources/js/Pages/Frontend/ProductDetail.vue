<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    relatedProducts: {
        type: Array,
        default: () => []
    }
});

// Debug
console.log('Product:', props.product);
console.log('Category:', props.product.category);

const selectedImage = ref(props.product.image);
const showForm = ref(false);
const formData = ref({
    name: '',
    phone: '',
    address: '',
    size: 'M',
    quantity: 1,
    notes: ''
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const formattedPrice = computed(() => formatPrice(props.product.price));

const openWhatsApp = () => {
    const message = `Halo, saya tertarik dengan produk ${props.product.name} (${window.location.href})`;
    const whatsappNumber = '6281234567890'; // Ganti dengan nomor WA toko
    window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`, '_blank');
};

const submitOrder = () => {
    // Di sini bisa ditambahkan logika untuk mengirim data ke backend
    const message = `Halo, saya ingin memesan:
Produk: ${props.product.name}
Ukuran: ${formData.value.size}
Jumlah: ${formData.value.quantity}
Nama: ${formData.value.name}
No. HP: ${formData.value.phone}
Alamat: ${formData.value.address}
Catatan: ${formData.value.notes}`;

    const whatsappNumber = '6281234567890'; // Ganti dengan nomor WA toko
    window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`, '_blank');
    showForm.value = false;
    formData.value = {
        name: '',
        phone: '',
        address: '',
        size: 'M',
        quantity: 1,
        notes: ''
    };
};
</script>

<template>

    <Head :title="product.name + ' | BNS'" />

    <FrontendLayout>
        <div class="bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
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
                                <Link :href="route('products')"
                                    class="text-gray-500 dark:text-gray-400 hover:text-emerald-600">
                                Produk
                                </Link>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 9 4-4-4-4" />
                                </svg>
                                <Link :href="route('categories.show', { category: product.category.name })"
                                    class="text-gray-500 dark:text-gray-400 hover:text-emerald-600">
                                {{ product.category.name }}
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
                                <span class="text-gray-500 dark:text-gray-400">{{ product.name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="lg:grid lg:grid-cols-2 lg:gap-x-8">
                    <!-- Image gallery -->
                    <div class="flex flex-col">
                        <div class="aspect-h-1 aspect-w-1 overflow-hidden rounded-lg">
                            <img :src="selectedImage" :alt="product.name"
                                class="h-full w-full object-cover object-center">
                        </div>

                        <!-- Image selector -->
                        <div class="mt-4 grid grid-cols-4 gap-4">
                            <button @click="selectedImage = product.image"
                                class="relative aspect-1 overflow-hidden rounded-lg border"
                                :class="{ 'ring-2 ring-emerald-500': selectedImage === product.image }">
                                <img :src="product.image" :alt="product.name"
                                    class="h-full w-full object-cover object-center">
                                <span class="sr-only">Load image 1 in gallery view</span>
                            </button>
                            <!-- Tambahkan button lain untuk gambar tambahan jika ada -->
                        </div>
                    </div>

                    <!-- Product info -->
                    <div class="mt-10 px-4 sm:mt-16 sm:px-0 lg:mt-0">
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ product.name }}
                        </h1>

                        <div class="mt-3">
                            <h2 class="sr-only">Product information</h2>
                            <p class="text-3xl tracking-tight text-gray-900 dark:text-white">{{ formattedPrice }}</p>
                        </div>

                        <div class="mt-6">
                            <h3 class="sr-only">Description</h3>
                            <div class="space-y-6 text-base text-gray-700 dark:text-gray-300"
                                v-html="product.description"></div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Kategori:</h4>
                                <Link :href="route('categories')"
                                    class="ml-2 text-sm text-emerald-600 hover:text-emerald-500">
                                {{ product.category.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Size guide -->
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Panduan Ukuran</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-800">
                                            <th
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Ukuran</th>
                                            <th
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Panjang</th>
                                            <th
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Lebar</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">S</td>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">110 cm</td>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">90 cm</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">M</td>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">115 cm</td>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">95 cm</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">L</td>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">120 cm</td>
                                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">100 cm</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-10 flex space-x-4">
                            <button @click="openWhatsApp"
                                class="flex max-w-xs flex-1 items-center justify-center rounded-md border border-transparent bg-emerald-600 px-8 py-3 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-gray-50 sm:w-full">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12.001 2.002c-5.522 0-9.999 4.477-9.999 9.999 0 1.869.518 3.668 1.475 5.235l-1.475 5.764 5.764-1.475c1.567.957 3.366 1.475 5.235 1.475 5.522 0 9.999-4.477 9.999-9.999s-4.477-9.999-9.999-9.999zm0 18.175c-1.825 0-3.589-.505-5.127-1.462l-.371-.221-3.835.983.983-3.835-.221-.371c-.957-1.538-1.462-3.302-1.462-5.127 0-4.597 3.738-8.335 8.335-8.335s8.335 3.738 8.335 8.335-3.738 8.335-8.335 8.335z" />
                                </svg>
                                Beli via WhatsApp
                            </button>
                            <button @click="$inertia.visit(route('checkout', product))"
                                class="flex max-w-xs flex-1 items-center justify-center rounded-md border border-transparent bg-blue-600 px-8 py-3 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-50 sm:w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Modal -->
                <div v-if="showForm" class="fixed inset-0 z-10 overflow-y-auto">
                    <div
                        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true">&#8203;</span>

                        <div
                            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                            Form Pemesanan
                                        </h3>
                                        <div class="mt-4">
                                            <form @submit.prevent="submitOrder">
                                                <div class="mb-4">
                                                    <label
                                                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                                                        for="name">
                                                        Nama Lengkap
                                                    </label>
                                                    <input v-model="formData.name"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                                                        id="name" type="text" required>
                                                </div>
                                                <div class="mb-4">
                                                    <label
                                                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                                                        for="phone">
                                                        Nomor WhatsApp
                                                    </label>
                                                    <input v-model="formData.phone"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                                                        id="phone" type="tel" required>
                                                </div>
                                                <div class="mb-4">
                                                    <label
                                                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                                                        for="address">
                                                        Alamat Pengiriman
                                                    </label>
                                                    <textarea v-model="formData.address"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                                                        id="address" rows="3" required></textarea>
                                                </div>
                                                <div class="mb-4">
                                                    <label
                                                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                                                        for="size">
                                                        Ukuran
                                                    </label>
                                                    <select v-model="formData.size"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                                                        id="size">
                                                        <option value="S">S</option>
                                                        <option value="M">M</option>
                                                        <option value="L">L</option>
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label
                                                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                                                        for="quantity">
                                                        Jumlah
                                                    </label>
                                                    <input v-model="formData.quantity"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                                                        id="quantity" type="number" min="1" required>
                                                </div>
                                                <div class="mb-4">
                                                    <label
                                                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                                                        for="notes">
                                                        Catatan (Opsional)
                                                    </label>
                                                    <textarea v-model="formData.notes"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                                                        id="notes" rows="2"></textarea>
                                                </div>
                                                <div class="flex justify-end space-x-3">
                                                    <button type="button" @click="showForm = false"
                                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                        class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                                        Kirim Pesanan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related products -->
                <div v-if="relatedProducts.length > 0" class="mt-16">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Produk Terkait</h2>

                    <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                        <div v-for="product in relatedProducts" :key="product.id" class="group relative">
                            <div
                                class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                                <img :src="product.image" :alt="product.name"
                                    class="h-full w-full object-cover object-center lg:h-full lg:w-full">
                            </div>
                            <div class="mt-4 flex justify-between">
                                <div>
                                    <h3 class="text-sm text-gray-700 dark:text-gray-200">
                                        <Link :href="route('products.show', {
                                            category: product.category.name,
                                            product: product.slug
                                        })">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        {{ product.name }}
                                        </Link>
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ product.category.name }}
                                    </p>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{
                                    formatPrice(product.price) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>
