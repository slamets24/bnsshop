<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SlideNotification from '@/Components/SlideNotification.vue';

const props = defineProps({
    product: Object,
    categories: Array,
    flash: Object
});

const productForm = useForm({
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
    stock: props.product.stock,
    category_id: props.product.category_id,
});

const linksForm = useForm({
    shopee_url: props.product.links?.shopee_url || '',
    tokopedia_url: props.product.links?.tokopedia_url || '',
    whatsapp_number: props.product.links?.whatsapp_number || ''
});

const imagesForm = useForm({
    images: [],
});

const imagePreview = ref([]);
const skuPreview = ref(props.product.sku);
const originalCategoryId = props.product.category_id;
const showDeleteModal = ref(false);
const imageToDelete = ref(null);
const activeTab = ref('product');

const notification = ref({
    show: false,
    message: '',
    type: 'success',
});

watch(() => productForm.category_id, (newCategoryId) => {
    if (newCategoryId === originalCategoryId) {
        skuPreview.value = props.product.sku;
    } else if (newCategoryId) {
        const category = props.categories.find(c => c.id === parseInt(newCategoryId));
        if (category) {
            skuPreview.value = `${category.code_sku}-${String(1).padStart(3, '0')}*`;
        }
    } else {
        skuPreview.value = '';
    }
});

watch(
    () => usePage().props.flash,
    (flash) => {
        if (flash.message) {
            notification.value = {
                show: true,
                message: flash.message.text,
                type: flash.message.type
            };
        }
    }
);

const showNotification = (type, message) => {
    notification.value = {
        show: true,
        type,
        message
    };
};

const handleImagesUpload = (e) => {
    const files = e.target.files;
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    const maxSize = 2048 * 1024;

    const validFiles = Array.from(files).filter(file => {
        const isValidType = allowedTypes.includes(file.type);
        const isValidSize = file.size <= maxSize;

        if (!isValidType || !isValidSize) {
            console.error(`File ${file.name} validation failed: ${!isValidType ? 'Invalid type' : 'Size too large'}`);
        }

        return isValidType && isValidSize;
    });

    imagesForm.images = [...imagesForm.images, ...validFiles];

    validFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    imagePreview.value.splice(index, 1);
    imagesForm.images.splice(index, 1);
};

const deleteExistingImage = (imageId) => {
    imageToDelete.value = imageId;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!imageToDelete.value) return;

    router.delete(route('dashboard.products.images.delete', {
        product: props.product,
        image: imageToDelete.value
    }), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            imageToDelete.value = null;
            router.reload({ only: ['product', 'flash'] });
        },
        onError: () => {
            showDeleteModal.value = false;
            imageToDelete.value = null;
        }
    });
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    imageToDelete.value = null;
};

const validateImageCount = () => {
    const totalImages = (props.product.images?.length || 0) + imagePreview.value.length;
    if (totalImages < 1) {
        showNotification('error', 'Produk harus memiliki minimal 1 gambar.');
        return false;
    }
    return true;
};

const updateProduct = () => {
    productForm.put(route('dashboard.products.update', props.product), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification('success', 'Data produk berhasil diperbarui');
        }
    });
};

const updateLinks = () => {
    linksForm.put(route('dashboard.products.links.update', props.product), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification('success', 'Link marketplace berhasil diperbarui');
            router.reload({ only: ['product'] });
        }
    });
};

const uploadImages = () => {
    if (!validateImageCount()) return;

    imagesForm.post(route('dashboard.products.images.upload', props.product), {
        preserveScroll: true,
        onSuccess: () => {
            imagesForm.reset();
            imagePreview.value = [];
            router.reload({ only: ['product'] });
        }
    });
};

const quillOptions = {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['link']
        ]
    },
    formats: ['header', 'bold', 'italic', 'underline', 'list', 'bullet', 'link'],
    bounds: '.quill-editor'
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

                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <button @click="activeTab = 'product'"
                                class="inline-block p-4 rounded-t-lg dark:text-gray-400"
                                :class="activeTab === 'product' ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'">
                                Data Produk
                            </button>
                        </li>
                        <li class="mr-2">
                            <button @click="activeTab = 'images'"
                                class="inline-block p-4 rounded-t-lg dark:text-gray-400"
                                :class="activeTab === 'images' ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'">
                                Gambar Produk
                            </button>
                        </li>
                        <li class="mr-2">
                            <button @click="activeTab = 'links'"
                                class="inline-block p-4 rounded-t-lg dark:text-gray-400"
                                :class="activeTab === 'links' ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'">
                                Link Marketplace
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="updateProduct" class="max-w-6xl" v-if="activeTab === 'product'">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                        Produk</label>
                                    <input type="text" id="name" v-model="productForm.name"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': productForm.errors.name }" />
                                    <div v-if="productForm.errors.name"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ productForm.errors.name }}
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
                                        v-if="productForm.category_id !== originalCategoryId">
                                        SKU akan berubah karena kategori diubah
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <label for="price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga</label>
                                    <input type="number" id="price" v-model="productForm.price"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': productForm.errors.price }" />
                                    <div v-if="productForm.errors.price"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ productForm.errors.price }}
                                    </div>
                                </div>

                                <div>
                                    <label for="stock"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stok</label>
                                    <input type="number" id="stock" v-model="productForm.stock"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': productForm.errors.stock }" />
                                    <div v-if="productForm.errors.stock"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ productForm.errors.stock }}
                                    </div>
                                </div>

                                <div>
                                    <label for="category_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                                    <select id="category_id" v-model="productForm.category_id"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': productForm.errors.category_id }">
                                        <option value="">Pilih Kategori</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <div v-if="productForm.errors.category_id"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ productForm.errors.category_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                                <div class="quill-editor">
                                    <QuillEditor v-model:content="productForm.description" :options="quillOptions"
                                        contentType="html"
                                        class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white min-h-[300px]"
                                        :class="{ 'border-red-500': productForm.errors.description }" />
                                </div>
                                <div v-if="productForm.errors.description"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ productForm.errors.description }}
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50"
                                    :disabled="productForm.processing">
                                    Simpan Data Produk
                                </button>
                                <Link :href="route('dashboard.products.index')"
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                Batal
                                </Link>
                            </div>
                        </form>

                        <!-- Image Upload Form -->
                        <form @submit.prevent="uploadImages" class="max-w-6xl" v-if="activeTab === 'images'">
                            <!-- Image Upload Section -->
                            <div class="mb-6">
                                <label for="images"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload
                                    Gambar
                                    Baru</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-lg"
                                    :class="{ 'border-red-500': imagesForm.errors.images }">
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

                                <h3 class="mt-8 mb-3 text-lg font-semibold text-gray-700 dark:text-gray-300">Gambar
                                    Produk Saat
                                    Ini</h3>

                                <!-- Existing Product Images -->
                                <div v-if="product.images && product.images.length > 0"
                                    class="mt-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                                    <div v-for="image in product.images" :key="image.id"
                                        class="relative group w-24 h-24 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                                        <img :src="image.full_url || image.image_url"
                                            class="w-full h-full object-cover" />
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
                                <div v-if="imagesForm.errors.images"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ imagesForm.errors.images }}
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mt-6">
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50"
                                    :disabled="imagesForm.processing">
                                    Upload Gambar Baru
                                </button>
                            </div>
                        </form>

                        <!-- Marketplace Links Form -->
                        <form @submit.prevent="updateLinks" class="max-w-6xl" v-if="activeTab === 'links'">
                            <!-- Product Links Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <label for="shopee_url"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link
                                        Shopee</label>
                                    <input type="url" id="shopee_url" v-model="linksForm.shopee_url"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': linksForm.errors.shopee_url }" />
                                    <div v-if="linksForm.errors.shopee_url"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ linksForm.errors.shopee_url }}
                                    </div>
                                </div>

                                <div>
                                    <label for="tokopedia_url"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link
                                        Tokopedia</label>
                                    <input type="url" id="tokopedia_url" v-model="linksForm.tokopedia_url"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': linksForm.errors.tokopedia_url }" />
                                    <div v-if="linksForm.errors.tokopedia_url"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ linksForm.errors.tokopedia_url }}
                                    </div>
                                </div>

                                <div>
                                    <label for="whatsapp_number"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor
                                        WhatsApp</label>
                                    <input type="tel" id="whatsapp_number" v-model="linksForm.whatsapp_number"
                                        class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': linksForm.errors.whatsapp_number }" />
                                    <div v-if="linksForm.errors.whatsapp_number"
                                        class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ linksForm.errors.whatsapp_number }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50"
                                    :disabled="linksForm.processing">
                                    Simpan Link Marketplace
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationModal :show="showDeleteModal" @close="cancelDelete" @confirm="confirmDelete"
            :title="'Konfirmasi Penghapusan'"
            :message="'Apakah Anda yakin ingin menghapus gambar ini? Tindakan ini tidak dapat dibatalkan.'">
        </ConfirmationModal>

        <SlideNotification v-if="notification.show" :message="notification.message" :type="notification.type"
            @close="notification.show = false" />
    </AuthenticatedLayout>
</template>
