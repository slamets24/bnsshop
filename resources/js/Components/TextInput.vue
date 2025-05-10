<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    error: {
        type: Boolean,
        default: false
    }
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :class="{ 'border-2 border-red-500 dark:border-red-500 focus:border-red-500 focus:ring-red-500 shadow-red-100 dark:shadow-red-900/30': error }"
        v-model="model" ref="input" />
</template>
