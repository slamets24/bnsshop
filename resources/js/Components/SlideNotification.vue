<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { TransitionRoot } from '@headlessui/vue';
import { CheckCircleIcon, XCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    type: {
        type: String,
        default: 'success'
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 2000
    }
});

const emit = defineEmits(['close']);

let timeout;

onMounted(() => {
    if (props.show && props.duration > 0) {
        timeout = setTimeout(() => {
            emit('close');
        }, props.duration);
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
                    <div class="flex-shrink-0">
                        <CheckCircleIcon v-if="type === 'success'" class="h-6 w-6 text-green-400 dark:text-green-300" />
                        <XCircleIcon v-else class="h-6 w-6 text-red-400 dark:text-red-300" />
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
                            }" @click="emit('close')">
                            <XMarkIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </TransitionRoot>
</template>
