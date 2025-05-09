<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SlideNotification from '@/Components/SlideNotification.vue';

const props = defineProps({
    shipment: Object,
    errors: Object,
    message: String
});

const form = useForm({
    tracking_number: props.shipment.tracking_number,
    courier: props.shipment.courier,
    shipping_cost: props.shipment.shipping_cost,
    status: props.shipment.status,
    estimated_delivery: props.shipment.estimated_delivery,
    note: props.shipment.note || ''
});

const notification = ref({
    show: false,
    type: 'success',
    message: ''
});

const statusOptions = [
    { value: 'processing', label: 'Memproses' },
    { value: 'shipped', label: 'Dikirim' },
    { value: 'delivered', label: 'Diterima' },
    { value: 'failed', label: 'Gagal' }
];

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

// Format nilai awal
onMounted(() => {
    if (form.shipping_cost) {
        form.shipping_cost = formatCurrency(form.shipping_cost);
    }
});

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
    }

    form.put(route('dashboard.shipments.update', props.shipment.id), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification('success', 'Pengiriman berhasil diperbarui');
        },
        onError: (errors) => {
            console.log('Form data:', form);
            console.log('Validation errors:', errors);

            // Menampilkan error validasi di masing-masing field
            if (Object.keys(errors).length > 0) {
                showNotification('error', 'Mohon perbaiki kesalahan input');
            }
        }
    });
};
</script>

<template>

    <Head title="Edit Pengiriman" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Pengiriman
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
                                    Edit Pengiriman
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tracking Number -->
                                <div>
                                    <InputLabel for="tracking_number" value="No. Resi" />
                                    <TextInput id="tracking_number" type="text"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.tracking_number }"
                                        v-model="form.tracking_number" required autofocus />
                                    <div v-if="form.errors.tracking_number"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ form.errors.tracking_number }}
                                    </div>
                                </div>

                                <!-- Courier -->
                                <div>
                                    <InputLabel for="courier" value="Kurir" />
                                    <TextInput id="courier" type="text"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.courier }"
                                        v-model="form.courier" required />
                                    <div v-if="form.errors.courier"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ form.errors.courier }}
                                    </div>
                                </div>

                                <!-- Shipping Cost -->
                                <div>
                                    <InputLabel for="shipping_cost" value="Biaya Pengiriman" />
                                    <TextInput id="shipping_cost" type="text" pattern="[0-9]*" inputmode="numeric"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.shipping_cost }"
                                        v-model="form.shipping_cost" required @input="handleCurrencyInput" />
                                    <div v-if="form.errors.shipping_cost"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ form.errors.shipping_cost }}
                                    </div>
                                </div>

                                <!-- Status -->
                                <div>
                                    <InputLabel for="status" value="Status" />
                                    <select id="status" v-model="form.status"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.status }" required>
                                        <option v-for="option in statusOptions" :key="option.value"
                                            :value="option.value">
                                            {{ option.label }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.status"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ form.errors.status }}
                                    </div>
                                </div>

                                <!-- Estimated Delivery -->
                                <div>
                                    <InputLabel for="estimated_delivery" value="Estimasi Pengiriman" />
                                    <TextInput id="estimated_delivery" type="datetime-local"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.estimated_delivery }"
                                        v-model="form.estimated_delivery" />
                                    <div v-if="form.errors.estimated_delivery"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                        {{ form.errors.estimated_delivery }}
                                    </div>
                                </div>
                            </div>

                            <!-- Note -->
                            <div>
                                <InputLabel for="note" value="Catatan" />
                                <textarea id="note"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500 dark:border-red-500': form.errors.note }"
                                    v-model="form.note" rows="3"></textarea>
                                <div v-if="form.errors.note"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400 font-semibold">
                                    {{ form.errors.note }}
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
