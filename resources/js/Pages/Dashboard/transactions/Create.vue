<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SlideNotification from '@/Components/SlideNotification.vue';

const props = defineProps({
    products: Array
});

const form = useForm({
    items: [],
    note: ''
});

const notification = ref({
    show: false,
    type: 'success',
    message: ''
});

const selectedProduct = ref(null);
const quantity = ref(1);

const filteredProducts = computed(() => {
    return props.products.filter(product => product.stock > 0);
});

const addItem = () => {
    if (!selectedProduct.value) {
        notification.value = {
            show: true,
            type: 'error',
            message: 'Pilih produk terlebih dahulu'
        };
        return;
    }

    if (quantity.value < 1) {
        notification.value = {
            show: true,
            type: 'error',
            message: 'Jumlah harus lebih dari 0'
        };
        return;
    }

    const product = props.products.find(p => p.id === selectedProduct.value);

    if (quantity.value > product.stock) {
        notification.value = {
            show: true,
            type: 'error',
            message: `Stok tidak mencukupi. Stok tersedia: ${product.stock}`
        };
        return;
    }

    const existingItem = form.items.find(item => item.product_id === selectedProduct.value);
    if (existingItem) {
        if (existingItem.quantity + quantity.value > product.stock) {
            notification.value = {
                show: true,
                type: 'error',
                message: `Stok tidak mencukupi. Stok tersedia: ${product.stock}`
            };
            return;
        }
        existingItem.quantity += quantity.value;
    } else {
        form.items.push({
            product_id: selectedProduct.value,
            product: product,
            quantity: quantity.value
        });
    }

    selectedProduct.value = null;
    quantity.value = 1;
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('dashboard.transactions.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const total = computed(() => {
    return form.items.reduce((sum, item) => {
        return sum + (item.product.price * item.quantity);
    }, 0);
});
</script>

<template>

    <Head title="Tambah Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Transaksi Offline
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
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <Link :href="route('dashboard.transactions.index')"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                                Transaksi
                                </Link>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Tambah Transaksi Offline
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Error Summary Section -->
                        <div v-if="Object.keys(form.errors).length > 0"
                            class="mb-6 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                                Mohon perbaiki kesalahan berikut:
                            </h3>
                            <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
                                <li v-for="(error, key) in form.errors" :key="key">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <form @submit.prevent="submit" class="max-w-6xl">
                            <!-- Add Items Section -->
                            <div class="space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="product"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Produk <span class="text-red-500">*</span>
                                        </label>
                                        <select v-model="selectedProduct" id="product"
                                            class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                            :class="{ 'border-red-500': form.errors.product }">
                                            <option value="">Pilih Produk</option>
                                            <option v-for="product in filteredProducts" :key="product.id"
                                                :value="product.id">
                                                {{ product.name }} (Stok: {{ product.stock }})
                                            </option>
                                        </select>
                                        <div v-if="form.errors.product"
                                            class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.product }}
                                        </div>
                                    </div>
                                    <div>
                                        <label for="quantity"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Jumlah <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" id="quantity" v-model="quantity"
                                            class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                            :class="{ 'border-red-500': form.errors.quantity }" min="1" />
                                        <div v-if="form.errors.quantity"
                                            class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.quantity }}
                                        </div>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" @click="addItem"
                                            class="w-full px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                            Tambah Item
                                        </button>
                                    </div>
                                </div>

                                <!-- Items List -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                        Item Transaksi <span class="text-red-500">*</span>
                                    </label>
                                    <div
                                        class="border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm">
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
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Aksi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <tr v-for="(item, index) in form.items" :key="index">
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
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <button type="button" @click="removeItem(index)"
                                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr v-if="form.items.length === 0">
                                                    <td colspan="5"
                                                        class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                        Belum ada item
                                                    </td>
                                                </tr>
                                                <tr v-if="form.items.length > 0"
                                                    class="bg-gray-50 dark:bg-gray-700 font-medium">
                                                    <td colspan="3"
                                                        class="px-6 py-4 text-right text-sm text-gray-900 dark:text-gray-100">
                                                        Total
                                                    </td>
                                                    <td colspan="2"
                                                        class="px-6 py-4 text-left text-sm text-gray-900 dark:text-gray-100">
                                                        {{ formatPrice(total) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-if="form.errors.items" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.items }}
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="note"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Catatan
                                    </label>
                                    <textarea id="note" v-model="form.note"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.note }" rows="3"></textarea>
                                    <div v-if="form.errors.note" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.note }}
                                    </div>
                                </div>
                            </div>

                            <!-- Add note about required fields -->
                            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="text-red-500">*</span> Wajib diisi
                            </div>

                            <div class="flex items-center gap-4 mt-8">
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50"
                                    :disabled="form.processing">
                                    Simpan
                                </button>
                                <Link :href="route('dashboard.transactions.index')"
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                Batal
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <SlideNotification :show="notification.show" :type="notification.type" :message="notification.message"
            @close="notification.show = false" />
    </AuthenticatedLayout>
</template>
