<template>
    <section class="w-full py-12 sm:py-24 lg:py-32">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid gap-6 lg:grid-cols-[1fr_400px] lg:gap-12 xl:grid-cols-[1fr_600px]">
                <div class="lg:col-span-1 lg:order-last">
                    <img src="/img/hero.jpg" width="550" height="550" alt="Featured Product"
                        class="aspect-square overflow-hidden rounded-xl object-cover sm:w-full hidden lg:block" />
                </div>
                <div class="flex flex-col justify-start space-y-4 lg:justify-center lg:items-start">
                    <div class="space-y-2">
                        <h1 class="text-3xl font-bold tracking-tighter sm:text-5xl xl:text-6xl/none dark:text-white">
                            Tampil Anggun dan Syar'i
                        </h1>
                        <p class="max-w-[600px] text-gray-600 dark:text-gray-300 md:text-xl">
                            Temukan koleksi terbaru busana muslimah yang elegan dan berkualitas tinggi untuk
                            menambah kecantikan dan kenyamanan dalam setiap langkah Anda.
                        </p>
                    </div>
                    <Link href="/products"
                        class="inline-flex h-10 items-center justify-center rounded-md bg-emerald-600 dark:bg-emerald-500 px-8 text-sm font-medium text-white shadow transition-colors hover:bg-emerald-500 dark:hover:bg-emerald-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600">
                    Belanja Sekarang
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
