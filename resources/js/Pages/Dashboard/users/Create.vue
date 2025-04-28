<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import SlideNotification from '@/Components/SlideNotification.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const notification = ref({
    show: false,
    type: 'success',
    message: ''
});

const submit = () => {
    form.post(route('dashboard.users.store'), {
        onSuccess: () => {
            showNotification('success', 'Admin berhasil ditambahkan');
            form.reset();
        },
        onError: () => {
            showNotification('error', 'Gagal menambahkan admin');
        }
    });
};

const showNotification = (type, message) => {
    notification.value = {
        show: true,
        type,
        message
    };
};
</script>

<template>

    <Head title="Tambah Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Admin
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
                                <Link :href="route('dashboard.users.index')"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                                Admin
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
                                    Tambah Admin
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

                        <form @submit.prevent="submit" class="max-w-6xl space-y-8">
                            <!-- Name and Email Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nama <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" v-model="form.name"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.name }" />
                                    <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" v-model="form.email"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.email }" />
                                    <div v-if="form.errors.email" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                            </div>

                            <!-- Password Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" id="password" v-model="form.password"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.password }" />
                                    <div v-if="form.errors.password"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.password }}
                                    </div>
                                </div>

                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Konfirmasi Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.password_confirmation }" />
                                    <div v-if="form.errors.password_confirmation"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.password_confirmation }}
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
                                <Link :href="route('dashboard.users.index')"
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
