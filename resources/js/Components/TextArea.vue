<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: String,
    rows: {
        type: [Number, String],
        default: 4
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <textarea
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm w-full"
        :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" :rows="rows"></textarea>
</template>
