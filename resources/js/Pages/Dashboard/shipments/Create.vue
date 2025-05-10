<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SlideNotification from '@/Components/SlideNotification.vue';

const props = defineProps({
    transactions: Array,
    selectedTransaction: Object,
    errors: Object
});

const form = useForm({
    transaction_id: props.selectedTransaction ? props.selectedTransaction.id : '',
    courier: '',
    tracking_number: '',
    shipping_cost: '',
    estimated_delivery: '',
    note: ''
});

const notification = ref({
    show: false,
    type: 'success',
    message: ''
});

const formatCurrency = (value) => {
    if (!value) return '';

    // Pastikan value adalah string
    value = String(value);

    // Hapus semua karakter selain angka
    const numericValue = value.replace(/[^0-9]/g, '');

    // Format dengan pemisah ribuan
    return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const handleCurrencyInput = (e) => {
    // Format nilai input saat user ketik
    let value = e.target.value;
    if (value) {
        const numericValue = String(value).replace(/[^0-9]/g, '');
        form.shipping_cost = formatCurrency(numericValue);
    }
};

onMounted(() => {
    if (props.selectedTransaction) {
        form.transaction_id = props.selectedTransaction.id;
    }

    if (form.shipping_cost) {
        form.shipping_cost = formatCurrency(form.shipping_cost);
    }
});

const showNotification = (type, message) => {
    notification.value = {
        show: true,
        type,
        message: message
    };
};

const submit = () => {
    // Pastikan shipping_cost adalah number
    if (form.shipping_cost) {
        try {
            // Pastikan menjadi string dulu sebelum replace
            const cleanValue = String(form.shipping_cost).replace(/[^0-9]/g, '');
            form.shipping_cost = Number(cleanValue);
        } catch (error) {
            console.error('Error converting shipping_cost:', error);
            form.shipping_cost = 0;
        }
    } else {
        // Set default ke 0 jika kosong
        form.shipping_cost = 0;
    }

    form.post(route('dashboard.shipments.store'), {
        onSuccess: () => {
            showNotification('success', 'Pengiriman berhasil dibuat');
            form.reset();
        },
        onError: (errors) => {
            console.log('Form data:', form);
            console.log('Validation errors:', errors);

            // Menangani error spesifik
            if (errors.error) {
                // Ini adalah error dari server (exception)
                const errorMessage = Array.isArray(errors.error) ? errors.error[0] : String(errors.error);
                showNotification('error', errorMessage);
            } else if (errors.shipping_cost) {
                // Error pada shipping_cost - lebih spesifik
                const errorMessage = Array.isArray(errors.shipping_cost) ? errors.shipping_cost[0] : String(errors.shipping_cost);
                showNotification('error', 'Biaya pengiriman tidak valid: ' + errorMessage);
            } else if (errors.transaction_id) {
                // Error pada transaction_id
                const errorMessage = Array.isArray(errors.transaction_id) ? errors.transaction_id[0] : String(errors.transaction_id);
                showNotification('error', 'Transaksi: ' + errorMessage);
            } else if (errors.tracking_number) {
                // Error pada tracking_number
                const errorMessage = Array.isArray(errors.tracking_number) ? errors.tracking_number[0] : String(errors.tracking_number);
                showNotification('error', 'Nomor resi: ' + errorMessage);
            } else if (errors.courier) {
                // Error pada courier
                const errorMessage = Array.isArray(errors.courier) ? errors.courier[0] : String(errors.courier);
                showNotification('error', 'Kurir: ' + errorMessage);
            } else if (Object.keys(errors).length > 0) {
                // Ambil error pertama untuk ditampilkan
                const firstErrorKey = Object.keys(errors)[0];
                const errorMessage = errors[firstErrorKey];
                if (Array.isArray(errorMessage)) {
                    showNotification('error', `Error pada ${firstErrorKey}: ${errorMessage[0]}`);
                } else {
                    showNotification('error', `Error pada ${firstErrorKey}: ${String(errorMessage)}`);
                }
            } else {
                // Fallback untuk error umum
                showNotification('error', 'Ada kesalahan saat menyimpan data. Mohon periksa kembali formulir Anda.');
            }
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
</script>

<template>

    <Head title="Buat Pengiriman" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Buat Pengiriman
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
                                <Link :href="route('dashboard.shipments.index')"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                                Pengiriman
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
                                    Buat Pengiriman
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Alert Error -->
                        <div v-if="form.errors.error"
                            class="p-4 mb-4 text-sm rounded-lg bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500 dark:text-red-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">
                                    {{ Array.isArray(form.errors.error) ? form.errors.error[0] : form.errors.error }}
                                </span>
                            </div>
                        </div>

                        <!-- Error Summary Section -->
                        <div v-if="Object.keys(form.errors).length > 0 && !form.errors.error"
                            class="mb-6 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                                Ada beberapa kesalahan pada form:
                            </h3>
                            <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
                                <li v-for="(error, key) in form.errors" :key="key" v-if="key !== 'error'">
                                    {{ Array.isArray(error) ? error[0] : error }}
                                </li>
                            </ul>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Transaction -->
                                <div>
                                    <InputLabel for="transaction_id" value="Transaksi" />
                                    <select id="transaction_id" v-model="form.transaction_id"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.transaction_id }"
                                        required>
                                        <option value="">Pilih transaksi</option>
                                        <option v-for="transaction in transactions" :key="transaction.id"
                                            :value="transaction.id">
                                            {{ transaction.transaction_code }} - {{ formatPrice(transaction.total) }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.transaction_id"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ Array.isArray(form.errors.transaction_id) ? form.errors.transaction_id[0] :
                                        form.errors.transaction_id }}
                                    </div>
                                </div>

                                <!-- Courier -->
                                <div>
                                    <InputLabel for="courier" value="Kurir" />
                                    <input id="courier" type="text"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.courier }"
                                        v-model="form.courier" required />
                                    <div v-if="form.errors.courier"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ Array.isArray(form.errors.courier) ? form.errors.courier[0] :
                                        form.errors.courier }}
                                    </div>
                                </div>

                                <!-- Tracking Number -->
                                <div>
                                    <InputLabel for="tracking_number" value="Nomor Resi" />
                                    <input id="tracking_number" type="text"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.tracking_number }"
                                        v-model="form.tracking_number" required />
                                    <div v-if="form.errors.tracking_number"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ Array.isArray(form.errors.tracking_number) ? form.errors.tracking_number[0] :
                                        form.errors.tracking_number }}
                                    </div>
                                </div>

                                <!-- Shipping Cost -->
                                <div>
                                    <InputLabel for="shipping_cost" value="Biaya Pengiriman" />
                                    <input id="shipping_cost" type="text"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.shipping_cost }"
                                        v-model="form.shipping_cost" @input="handleCurrencyInput" required />
                                    <div v-if="form.errors.shipping_cost"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ Array.isArray(form.errors.shipping_cost) ? form.errors.shipping_cost[0] :
                                        form.errors.shipping_cost }}
                                    </div>
                                </div>

                                <!-- Estimated Delivery -->
                                <div>
                                    <InputLabel for="estimated_delivery" value="Estimasi Pengiriman" />
                                    <input id="estimated_delivery" type="datetime-local"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.estimated_delivery }"
                                        v-model="form.estimated_delivery" />
                                    <div v-if="form.errors.estimated_delivery"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ Array.isArray(form.errors.estimated_delivery) ?
                                            form.errors.estimated_delivery[0] :
                                        form.errors.estimated_delivery }}
                                    </div>
                                </div>
                            </div>

                            <!-- Note -->
                            <div>
                                <InputLabel for="note" value="Catatan" />
                                <textarea id="note" v-model="form.note"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500 dark:border-red-500': form.errors.note }"
                                    rows="3"></textarea>
                                <div v-if="form.errors.note"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                    {{ Array.isArray(form.errors.note) ? form.errors.note[0] : form.errors.note }}
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                                    Simpan
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <SlideNotification :show="notification?.show || false" :type="notification?.type || 'success'"
            :message="notification?.message || ''" @close="notification.show = false" />
    </AuthenticatedLayout>
</template>
