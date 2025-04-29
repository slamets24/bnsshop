<script setup>
defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6">
        <div v-for="(link, key) in links" :key="key" class="flex-1 flex justify-center">
            <Link v-if="link.url" :href="link.url"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium" :class="{
                    'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700': !link.active,
                    'z-10 bg-emerald-600 text-white': link.active
                }" v-html="link.label" />
            <span v-else
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400"
                v-html="link.label" />
        </div>
    </div>
</template>
