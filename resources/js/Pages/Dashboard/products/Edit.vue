<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
    product: Object,
    categories: Array
});

const form = useForm({
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
    stock: props.product.stock,
    category_id: props.product.category_id,
    images: [],
    shopee_url: props.product.shopee_url,
    tokopedia_url: props.product.tokopedia_url,
    whatsapp_number: props.product.whatsapp_number
});

const imagePreview = ref([]);
const skuPreview = ref(props.product.sku);
const originalCategoryId = props.product.category_id;

// Watch for category changes to update SKU preview
watch(() => form.category_id, (newCategoryId) => {
    if (newCategoryId === originalCategoryId) {
        // If category hasn't changed, show original SKU
        skuPreview.value = props.product.sku;
    } else if (newCategoryId) {
        const category = props.categories.find(c => c.id === parseInt(newCategoryId));
        if (category) {
            // Generate preview of new SKU
            const productCount = 1; // This will be handled by backend
            skuPreview.value = `${category.code_sku}-${String(productCount).padStart(3, '0')}*`;
        }
    } else {
        skuPreview.value = '';
    }
});

const router = useRouter();

const handleImagesUpload = (e) => {
    const files = e.target.files;
    // Append new files instead of replacing
    form.images = [...form.images, ...files];

    // Generate previews for new files only
    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    imagePreview.value.splice(index, 1);
    const newImages = [...form.images];
    newImages.splice(index, 1);
    form.images = newImages;
};

const deleteExistingImage = (imageId) => {
    if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
        router.delete(route('dashboard.products.images.destroy', imageId), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // Image will be removed from the list automatically by Inertia refresh
            }
        });
    }
};

const submit = () => {
    form.post(route('dashboard.products.update', props.product.id), {
        _method: 'PUT'
    });
};
</script>

<template>

    <Head title="Edit Produk" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Produk
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
                                <Link :href="route('dashboard.products.index')"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                                Produk
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
                                    Edit {{ product.name }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="max-w-6xl">
                            <!-- Image Upload Section -->
                            <div class="mb-6">
                                <label for="images"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gambar
                                    Produk</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-lg"
                                    :class="{ 'border-red-500': form.errors.images }">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                            <label for="images"
                                                class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload files</span>
                                                <input id="images" type="file" class="sr-only" multiple
                                                    @change="handleImagesUpload" accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            PNG, JPG, GIF up to 10MB each
                                        </p>
                                    </div>
                                </div>
                                <!-- Image Preview -->
                                <div v-if="imagePreview.length"
                                    class="mt-6 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                                    <div v-for="(preview, index) in imagePreview" :key="index"
                                        class="relative group w-24 h-24 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                                        <img :src="preview" class="w-full h-full object-cover" />
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <button type="button" @click.prevent="removeImage(index)"
                                                class="p-1.5 rounded-full bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Existing Product Images -->
                                <div v-if="product.images && product.images.length > 0"
                                    class="mt-6 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                                    <div v-for="image in product.images" :key="image.id"
                                        class="relative group w-24 h-24 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                                        <img :src="image.url" class="w-full h-full object-cover" />
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <button type="button" @click.prevent="deleteExistingImage(image.id)"
                                                class="p-1.5 rounded-full bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.images" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.images }}
                                </div>
                            </div>

                            <!-- Name and SKU Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                        Produk</label>
                                    <input type="text" id="name" v-model="form.name"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.name }" />
                                    <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div>
                                    <label for="sku"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU
                                        (Auto-generated)</label>
                                    <input type="text" id="sku" :value="skuPreview"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-600 cursor-not-allowed"
                                        readonly />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                        v-if="form.category_id !== originalCategoryId">
                                        SKU akan berubah karena kategori diubah
                                    </p>
                                </div>
                            </div>

                            <!-- Price, Stock, and Category Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <label for="price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga</label>
                                    <input type="number" id="price" v-model="form.price"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.price }" />
                                    <div v-if="form.errors.price" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.price }}
                                    </div>
                                </div>

                                <div>
                                    <label for="stock"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stok</label>
                                    <input type="number" id="stock" v-model="form.stock"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.stock }" />
                                    <div v-if="form.errors.stock" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.stock }}
                                    </div>
                                </div>

                                <div>
                                    <label for="category_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                                    <select id="category_id" v-model="form.category_id"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.category_id }">
                                        <option value="">Pilih Kategori</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.category_id"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.category_id }}
                                    </div>
                                </div>
                            </div>

                            <!-- Product Links Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <label for="shopee_url"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link
                                        Shopee</label>
                                    <input type="url" id="shopee_url" v-model="form.shopee_url"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.shopee_url }" />
                                    <div v-if="form.errors.shopee_url"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.shopee_url }}
                                    </div>
                                </div>

                                <div>
                                    <label for="tokopedia_url"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link
                                        Tokopedia</label>
                                    <input type="url" id="tokopedia_url" v-model="form.tokopedia_url"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.tokopedia_url }" />
                                    <div v-if="form.errors.tokopedia_url"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.tokopedia_url }}
                                    </div>
                                </div>

                                <div>
                                    <label for="whatsapp_number"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor
                                        WhatsApp</label>
                                    <input type="tel" id="whatsapp_number" v-model="form.whatsapp_number"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.whatsapp_number }" />
                                    <div v-if="form.errors.whatsapp_number"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.whatsapp_number }}
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                                <textarea id="description" v-model="form.description" rows="4"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.description }"></textarea>
                                <div v-if="form.errors.description" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50"
                                    :disabled="form.processing">
                                    Simpan
                                </button>
                                <Link :href="route('dashboard.products.index')"
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                Batal
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
