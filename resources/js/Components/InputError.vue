<script setup>
import { ref } from 'vue';

const props = defineProps({
    message: {
        type: String,
    },
});

console.log('InputError props:', props);
</script>

<template>
    <div v-if="message">
        <p class="text-sm text-red-600 dark:text-red-400 font-semibold mt-1">
            {{ message }}
        </p>
    </div>
</template>
