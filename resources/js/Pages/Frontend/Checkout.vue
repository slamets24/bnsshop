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

const submit = () => {
    form.post(route('order.store'));
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
                                        Nama Penerima
                                    </label>
                                    <input type="text" id="recipient_name" v-model="form.recipient_name" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                </div>

                                <div>
                                    <label for="phone_number"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nomor WhatsApp
                                    </label>
                                    <input type="tel" id="phone_number" v-model="form.phone_number" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                </div>

                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Email
                                    </label>
                                    <input type="email" id="email" v-model="form.email" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                </div>

                                <div>
                                    <label for="address"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Alamat Lengkap
                                    </label>
                                    <textarea id="address" v-model="form.address" rows="3" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="province"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Provinsi
                                        </label>
                                        <input type="text" id="province" v-model="form.province" required
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                    </div>

                                    <div>
                                        <label for="city"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Kota/Kabupaten
                                        </label>
                                        <input type="text" id="city" v-model="form.city" required
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="district"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Kecamatan
                                        </label>
                                        <input type="text" id="district" v-model="form.district" required
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                    </div>

                                    <div>
                                        <label for="postal_code"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Kode Pos
                                        </label>
                                        <input type="text" id="postal_code" v-model="form.postal_code" required
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                    </div>
                                </div>

                                <div>
                                    <label for="payment_method"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Metode Pembayaran
                                    </label>
                                    <select id="payment_method" v-model="form.payment_method" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                        <option value="">Pilih metode pembayaran</option>
                                        <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                                            {{ method.name }}
                                        </option>
                                    </select>
                                </div>

                                <div v-if="showPaymentChannels">
                                    <label for="payment_channel"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Channel Pembayaran
                                    </label>
                                    <select id="payment_channel" v-model="form.payment_channel" required
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white">
                                        <option value="">Pilih channel pembayaran</option>
                                        <option v-for="channel in paymentChannels[form.payment_method]"
                                            :key="channel.id" :value="channel.id">
                                            {{ channel.name }}
                                        </option>
                                    </select>
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
