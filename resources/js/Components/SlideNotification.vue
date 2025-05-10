<script setup>
import { onMounted, onUnmounted } from 'vue';
import { TransitionRoot } from '@headlessui/vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    message: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'success'
    }
});

const emit = defineEmits(['close']);

let timeout;

onMounted(() => {
    // Auto close after 3 seconds
    if (props.show) {
        timeout = setTimeout(() => {
            emit('close');
        }, 3000);
    }
});

onUnmounted(() => {
    if (timeout) clearTimeout(timeout);
});
</script>

<template>
    <TransitionRoot appear :show="show" as="template" enter="transform transition ease-in-out duration-500"
        enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500"
        leave-from="translate-x-0" leave-to="translate-x-full">
        <div class="fixed right-0 top-16 z-50 mx-4 w-full max-w-sm overflow-hidden rounded-lg shadow-lg" :class="{
            'bg-green-50 dark:bg-green-900/50': type === 'success',
            'bg-red-50 dark:bg-red-900/50': type === 'error'
        }">
            <div class="p-4">
                <div class="flex items-start">
                    <!-- Success Icon -->
                    <div class="flex-shrink-0">
                        <svg v-if="type === 'success'" class="h-6 w-6 text-green-400 dark:text-green-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <!-- Error Icon -->
                        <svg v-else class="h-6 w-6 text-red-400 dark:text-red-300" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium" :class="{
                            'text-green-800 dark:text-green-100': type === 'success',
                            'text-red-800 dark:text-red-100': type === 'error'
                        }">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button type="button"
                            class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2" :class="{
                                'text-green-500 hover:text-green-600 focus:ring-green-500 focus:ring-offset-green-50 dark:text-green-300 dark:hover:text-green-400': type === 'success',
                                'text-red-500 hover:text-red-600 focus:ring-red-500 focus:ring-offset-red-50 dark:text-red-300 dark:hover:text-red-400': type === 'error'
                            }" @click="$emit('close')">
                            <span class="sr-only">Close</span>
                            <!-- Close Icon -->
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </TransitionRoot>
</template>

<style>
.translate-x-full {
    transform: translateX(100%);
}

.translate-x-0 {
    transform: translateX(0);
}

.transition {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 500ms;
}
</style>
