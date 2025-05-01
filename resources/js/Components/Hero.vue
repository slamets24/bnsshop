<template>
    <section class="w-full py-12 sm:py-24 lg:py-32 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 -z-10 opacity-5">
            <div class="absolute inset-0"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'30\' height=\'30\' viewBox=\'0 0 30 30\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\' fill=\'rgba(0,0,0,0.07)\'/%3E%3C/svg%3E')">
            </div>
        </div>

        <div class="container px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid gap-6 lg:grid-cols-[1fr_400px] lg:gap-12 xl:grid-cols-[1fr_600px]">
                <div class="lg:col-span-1 lg:order-last relative">
                    <!-- Image with decorative elements -->
                    <div class="relative">
                        <img src="/img/hero.jpg" width="550" height="550" alt="Featured Product"
                            class="aspect-square overflow-hidden rounded-xl object-cover sm:w-full hidden lg:block shadow-2xl" />
                        <!-- Decorative circles -->
                        <div
                            class="absolute -top-4 -right-4 w-24 h-24 bg-emerald-200 rounded-full opacity-50 dark:bg-emerald-700">
                        </div>
                        <div
                            class="absolute -bottom-4 -left-4 w-32 h-32 bg-emerald-100 rounded-full opacity-50 dark:bg-emerald-800">
                        </div>
                    </div>
                </div>
                <div class="flex flex-col justify-start space-y-8 lg:justify-center lg:items-start">
                    <div class="space-y-4">
                        <h1 class="text-3xl font-bold tracking-tighter sm:text-5xl xl:text-6xl/none dark:text-white">
                            Tampil Anggun dan <span class="text-emerald-600 dark:text-emerald-400">Syar'i</span>
                        </h1>
                        <p class="max-w-[600px] text-gray-600 dark:text-gray-300 md:text-xl leading-relaxed">
                            Temukan koleksi terbaru busana muslimah yang elegan dan berkualitas tinggi untuk
                            menambah kecantikan dan kenyamanan dalam setiap langkah Anda.
                        </p>
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Bahan Premium</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Model Terkini</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Jahitan Rapi</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Pengiriman Cepat</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Link href="/products"
                            class="inline-flex h-12 items-center justify-center rounded-lg bg-emerald-600 dark:bg-emerald-500 px-8 text-base font-medium text-white shadow-lg transition-all duration-200 hover:bg-emerald-500 hover:shadow-emerald-200 dark:hover:bg-emerald-400 dark:hover:shadow-emerald-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 group">
                        Belanja Sekarang
                        <svg class="w-5 h-5 ml-2 transform transition-transform group-hover:translate-x-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                        </Link>
                        <a href="https://wa.me/6282240338227" target="_blank"
                            class="inline-flex h-12 items-center justify-center rounded-lg border border-emerald-600 dark:border-emerald-500 px-8 text-base font-medium text-emerald-600 dark:text-emerald-400 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600">
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const submit = () => {
    form.post(route('order.store'));
};
</script>
