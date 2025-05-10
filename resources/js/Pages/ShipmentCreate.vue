<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { router } from '@inertiajs/vue3';

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

onMounted(() => {
    if (props.selectedTransaction) {
        form.transaction_id = props.selectedTransaction.id;
    }
});

const submit = () => {
    form.post(route('dashboard.shipments.store'), {
        onSuccess: () => {
            router.visit(route('dashboard.shipments.index'));
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
                <nav class="mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link :href="route('dashboard')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
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
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Transaction -->
                                <div>
                                    <InputLabel for="transaction_id" value="Transaksi" />
                                    <select id="transaction_id" v-model="form.transaction_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm"
                                        required>
                                        <option value="">Pilih transaksi</option>
                                        <option v-for="transaction in transactions" :key="transaction.id"
                                            :value="transaction.id">
                                            {{ transaction.transaction_code }} - {{ formatPrice(transaction.total) }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.transaction_id" />
                                </div>

                                <!-- Courier -->
                                <div>
                                    <InputLabel for="courier" value="Kurir" />
                                    <TextInput id="courier" type="text" v-model="form.courier" class="mt-1 block w-full"
                                        required />
                                    <InputError class="mt-2" :message="form.errors.courier" />
                                </div>

                                <!-- Tracking Number -->
                                <div>
                                    <InputLabel for="tracking_number" value="Nomor Resi" />
                                    <TextInput id="tracking_number" type="text" v-model="form.tracking_number"
                                        class="mt-1 block w-full" required />
                                    <InputError class="mt-2" :message="form.errors.tracking_number" />
                                </div>

                                <!-- Shipping Cost -->
                                <div>
                                    <InputLabel for="shipping_cost" value="Biaya Pengiriman" />
                                    <TextInput id="shipping_cost" type="number" v-model="form.shipping_cost"
                                        class="mt-1 block w-full" required />
                                    <InputError class="mt-2" :message="form.errors.shipping_cost" />
                                </div>

                                <!-- Estimated Delivery -->
                                <div>
                                    <InputLabel for="estimated_delivery" value="Estimasi Pengiriman" />
                                    <TextInput id="estimated_delivery" type="datetime-local"
                                        v-model="form.estimated_delivery" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.estimated_delivery" />
                                </div>
                            </div>

                            <!-- Note -->
                            <div>
                                <InputLabel for="note" value="Catatan" />
                                <textarea id="note" v-model="form.note"
                                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                    rows="3"></textarea>
                                <InputError class="mt-2" :message="form.errors.note" />
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
    </AuthenticatedLayout>
</template>
