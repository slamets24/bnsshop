<script setup>
import { Head, Link } from '@inertiajs/vue3';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';

const props = defineProps({
    transaction: {
        type: Object,
        required: true
    }
});

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

    <Head title="Pesanan Berhasil" />

    <FrontendLayout>
        <div class="bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="max-w-2xl mx-auto">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-emerald-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Pesanan Berhasil Dibuat!</h2>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Terima kasih telah berbelanja di BNS Hijab. Kami akan segera memproses pesanan Anda.
                        </p>
                    </div>

                    <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <div class="border-b border-gray-200 dark:border-gray-600 pb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Pesanan</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Kode Transaksi: {{ transaction.transaction_code }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Tanggal: {{ formatDate(transaction.created_at) }}
                            </p>
                        </div>

                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Produk</h4>
                            <div class="mt-2">
                                <div v-for="item in transaction.items" :key="item.id" class="flex justify-between py-2">
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-white">{{ item.product.name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ item.quantity }} x {{ formatPrice(item.unit_price) }}
                                        </p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ formatPrice(item.subtotal) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                            <div class="flex justify-between">
                                <span class="text-base font-medium text-gray-900 dark:text-white">Total</span>
                                <span class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ formatPrice(transaction.total) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Alamat Pengiriman</h4>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <p>{{ transaction.shipping_address.recipient_name }}</p>
                                <p>{{ transaction.shipping_address.phone_number }}</p>
                                <p>{{ transaction.shipping_address.address }}</p>
                                <p>
                                    {{ transaction.shipping_address.district }},
                                    {{ transaction.shipping_address.city }},
                                    {{ transaction.shipping_address.province }}
                                    {{ transaction.shipping_address.postal_code }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Metode Pembayaran</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ transaction.payment_method }}
                                <span v-if="transaction.payment_channel">
                                    - {{ transaction.payment_channel }}
                                </span>
                            </p>
                        </div>

                        <div class="mt-6 flex justify-center space-x-4">
                            <Link :href="route('welcome')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Lanjut Belanja
                            </Link>
                            <Link :href="route('order.track', transaction.tracking_token)"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Lacak Pesanan
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>
