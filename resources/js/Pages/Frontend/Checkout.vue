<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    paymentMethods: {
        type: Array,
        required: true
    }
});

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
    recipient_name: '',
    phone_number: '',
    email: '',
    address: '',
    province: '',
    city: '',
    district: '',
    postal_code: '',
    payment_method: '',
    payment_channel: '',
    note: ''
});

// Debug the form errors - menambahkan ini untuk melacak error
const debug = () => {
    console.log('Current errors:', form.errors);
};

const showPaymentChannels = computed(() => {
    return ['bank_transfer', 'ewallet'].includes(form.payment_method);
});

const paymentChannels = {
    bank_transfer: [
        { id: 'bca', name: 'BCA' },
        { id: 'mandiri', name: 'Mandiri' },
        { id: 'bni', name: 'BNI' },
        { id: 'bri', name: 'BRI' }
    ],
    ewallet: [
        { id: 'gopay', name: 'GoPay' },
        { id: 'ovo', name: 'OVO' },
        { id: 'dana', name: 'DANA' },
        { id: 'shopeepay', name: 'ShopeePay' }
    ]
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const subtotal = computed(() => {
    return props.product.price * form.quantity;
});

const getErrorMessage = (field) => {
    if (!form.errors[field]) return '';
    return Array.isArray(form.errors[field]) ? form.errors[field][0] : form.errors[field];
};

const submit = () => {
    // Debug form before submit
    console.log('Form before submit:', { ...form });

    form.post(route('order.store'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Validation errors:', errors);

            // Pastikan error disimpan dengan benar
            Object.keys(errors).forEach(key => {
                form.setError(key, errors[key]);
            });

            // Scroll to first error if any
            setTimeout(() => {
                const firstErrorField = document.querySelector('.border-red-500');
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);

            // Debug after error
            console.log('Form after error:', { ...form });
        },
        onSuccess: () => {
            console.log('Order submitted successfully');
        }
    });
};
</script>

<template>

    <Head title="Checkout" />

    <FrontendLayout>
        <div class="bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-x-8">
                    <!-- Product Summary -->
                    <div class="lg:col-span-1">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Ringkasan Pesanan</h2>
                        <div class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center">
                                <img :src="product.image" :alt="product.name" class="h-20 w-20 object-cover rounded">
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ product.name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ product.category.name }}
                                    </p>
                                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{
                                        formatPrice(product.price) }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
                                <input type="number" v-model="form.quantity" min="1" :max="product.stock"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                            </div>
                            <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-base font-medium text-gray-900 dark:text-white">Subtotal</span>
                                    <span class="text-base font-medium text-gray-900 dark:text-white">{{
                                        formatPrice(subtotal) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <div class="mt-10 lg:mt-0 lg:col-span-1">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Pengiriman</h2>
                        <form @submit.prevent="submit" class="mt-4">
                            <div class="grid grid-cols-1 gap-y-6">
                                <div>
                                    <label for="recipient_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nama Penerima <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" id="recipient_name" v-model="form.recipient_name"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                            :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.recipient_name }">
                                        <p v-if="form.errors.recipient_name"
                                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ getErrorMessage('recipient_name') }}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label for="phone_number"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nomor WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input type="tel" id="phone_number" v-model="form.phone_number"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                            :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.phone_number }">
                                        <p v-if="form.errors.phone_number"
                                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ getErrorMessage('phone_number') }}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input type="email" id="email" v-model="form.email"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                            :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.email }">
                                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ getErrorMessage('email') }}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label for="address"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Alamat Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <textarea id="address" v-model="form.address" rows="3"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                            :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.address }"></textarea>
                                        <p v-if="form.errors.address"
                                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ getErrorMessage('address') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="province"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Provinsi <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" id="province" v-model="form.province"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.province }">
                                            <p v-if="form.errors.province"
                                                class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                {{ getErrorMessage('province') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="city"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Kota/Kabupaten <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" id="city" v-model="form.city"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.city }">
                                            <p v-if="form.errors.city"
                                                class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                {{ getErrorMessage('city') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="district"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Kecamatan <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" id="district" v-model="form.district"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.district }">
                                            <p v-if="form.errors.district"
                                                class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                {{ getErrorMessage('district') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="postal_code"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Kode Pos <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" id="postal_code" v-model="form.postal_code"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.postal_code }">
                                            <p v-if="form.errors.postal_code"
                                                class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                {{ getErrorMessage('postal_code') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="payment_method"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Metode Pembayaran <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <select id="payment_method" v-model="form.payment_method"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                            :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.payment_method }">
                                            <option value="">Pilih metode pembayaran</option>
                                            <option v-for="method in paymentMethods" :key="method.id"
                                                :value="method.id">
                                                {{ method.name }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.payment_method"
                                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ getErrorMessage('payment_method') }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="showPaymentChannels">
                                    <label for="payment_channel"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Channel Pembayaran <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <select id="payment_channel" v-model="form.payment_channel"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"
                                            :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.payment_channel }">
                                            <option value="">Pilih channel pembayaran</option>
                                            <option v-for="channel in paymentChannels[form.payment_method]"
                                                :key="channel.id" :value="channel.id">
                                                {{ channel.name }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.payment_channel"
                                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ getErrorMessage('payment_channel') }}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label for="note"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Catatan (Opsional)
                                    </label>
                                    <textarea id="note" v-model="form.note" rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"></textarea>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full rounded-md border border-transparent bg-emerald-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-gray-50">
                                    Buat Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>