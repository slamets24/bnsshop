<script setup>
import { Head } from '@inertiajs/vue3';
import FrontendLayout from '@/Layouts/FrontendLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Hero from '@/Components/Hero.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    featuredProducts: Array,
    products: Object,
    favoritProducts: Array
});
</script>

<template>

    <Head title="BNS | Beranda" />

    <FrontendLayout>
        <!-- Hero Section -->
        <Hero />

        <!-- Produk Terpopuler -->
        <section class="w-full py-10 sm:py-12 lg:py-14 bg-gray-100 dark:bg-gray-800">
            <div class="container mx-auto px-4 md:px-6">
                <h2 class="text-3xl font-bold tracking-tight text-center mb-8 dark:text-white">Produk Terpopuler
                </h2>
                <div class="grid gap-6 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4">
                    <ProductCard v-for="product in favoritProducts" :key="product.id" :product="product" />
                </div>
                <div class="mt-8 text-center">
                    <Link href="/products"
                        class="inline-flex h-10 items-center justify-center rounded-md bg-emerald-600 dark:bg-emerald-500 px-8 text-sm font-medium text-white shadow transition-colors hover:bg-emerald-500 dark:hover:bg-emerald-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600">
                    Lihat Selengkapnya
                    </Link>
                </div>
            </div>
        </section>

        <!-- Keunggulan Kami -->
        <section class="w-full py-10 sm:py-12 lg:py-14 bg-green-700 dark:bg-gray-900">
            <div class="container mx-auto px-4 md:px-6">
                <h2 class="text-3xl font-bold tracking-tight text-center mb-12 dark:text-white">Kenapa Harus BNS Hijab?
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex flex-col items-center text-center p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-600 mb-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Kualitas Terjamin</h3>
                        <p class="text-gray-600 dark:text-gray-300">Produk kami dibuat dengan bahan berkualitas tinggi
                            dan
                            jahitan yang rapi</p>
                    </div>
                    <div class="flex flex-col items-center text-center p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-600 mb-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Pengiriman Cepat</h3>
                        <p class="text-gray-600 dark:text-gray-300">Proses pengiriman yang cepat dengan packaging yang
                            aman</p>
                    </div>
                    <div class="flex flex-col items-center text-center p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-600 mb-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Layanan Responsif</h3>
                        <p class="text-gray-600 dark:text-gray-300">Customer service yang ramah dan responsif siap
                            membantu Anda
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tentang Kami -->
        <section class="w-full py-10 sm:py-12 lg:py-14 bg-gray-100 dark:bg-gray-800">
            <div class="container mx-auto px-4 md:px-6">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl xl:text-5xl mb-6 dark:text-white">Tentang Kami
                </h2>
                <div class="flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-8 mb-12">
                    <div class="lg:w-2/3 space-y-6">
                        <p class="text-gray-600 dark:text-gray-300 md:text-lg leading-relaxed">
                            Selamat datang di toko online kami yang menghadirkan koleksi busana muslimah terkini.
                            Kami memahami pentingnya tampil elegan dan tetap sesuai syariat, sehingga kami menyediakan
                            beragam pilihan busana yang tidak hanya stylish tetapi juga nyaman dan berkualitas tinggi.
                        </p>
                        <p class="text-gray-600 dark:text-gray-300 md:text-lg leading-relaxed">
                            Didirikan oleh sekelompok individu yang berdedikasi untuk menghadirkan pilihan terbaik bagi
                            wanita muslimah, kami selalu berusaha memberikan produk yang memenuhi kebutuhan dan selera
                            Anda.
                        </p>
                        <p class="text-gray-600 dark:text-gray-300 md:text-lg leading-relaxed">
                            Terima kasih telah mempercayakan kami sebagai pilihan utama Anda dalam mencari busana
                            muslimah. Kami berkomitmen untuk terus berinovasi dan menghadirkan produk-produk terbaik
                            bagi Anda.
                        </p>
                    </div>
                    <div class="lg:w-1/3">
                        <img src="/img/1.jpg" alt="Tentang Kami" class="w-full h-auto object-cover rounded-lg" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimoni Pelanggan -->
        <section class="w-full py-10 sm:py-12 lg:py-14 bg-white dark:bg-gray-900">
            <div class="container mx-auto px-4 md:px-6">
                <h2 class="text-3xl font-bold tracking-tight text-center mb-12 dark:text-white">Apa Kata Mereka?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold dark:text-white">Sarah Amalia</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Customer Setia</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">"Kualitas produknya bagus banget, jahitannya rapi.
                            Pengiriman juga cepat. Recommended banget!"</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold dark:text-white">Fatima Zahra</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Customer</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">"Bahannya adem dan nyaman dipakai. Admin ramah dan
                            fast
                            response. Pasti bakal repeat order!"</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold dark:text-white">Aisyah Putri</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Customer</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">"Model hijabnya up to date dan harganya terjangkau.
                            Cocok
                            untuk daily wear!"</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA/Kontak -->
        <section class="w-full py-10 sm:py-12 lg:py-14 bg-emerald-600 dark:bg-emerald-700">
            <div class="container mx-auto px-4 md:px-6 text-center">
                <h2 class="text-3xl font-bold tracking-tight text-white mb-4">Butuh Bantuan?</h2>
                <p class="text-lg text-white/90 mb-8">Hubungi kami melalui WhatsApp atau Instagram untuk konsultasi dan
                    pemesanan</p>
                <div class="flex justify-center space-x-4">
                    <a href="https://wa.me/your-number" target="_blank"
                        class="inline-flex items-center px-6 py-3 bg-white text-emerald-600 rounded-lg hover:bg-emerald-50 transition-colors">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                        </svg>
                        WhatsApp
                    </a>
                    <a href="https://instagram.com/bns_hijab" target="_blank"
                        class="inline-flex items-center px-6 py-3 bg-white text-emerald-600 rounded-lg hover:bg-emerald-50 transition-colors">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                        Instagram
                    </a>
                </div>
            </div>
        </section>

        <!-- Semua Produk -->
        <section class="w-full py-8 sm:py-10 lg:py-12 bg-gray-100 dark:bg-gray-800">
            <div class="container mx-auto px-4 md:px-6">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-center mb-6 sm:mb-8 dark:text-white">Semua
                    Produk</h2>

                <div class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                </div>

                <div class="mt-6">
                    <Pagination :links="products.links" />
                </div>
            </div>
        </section>
    </FrontendLayout>
</template>
