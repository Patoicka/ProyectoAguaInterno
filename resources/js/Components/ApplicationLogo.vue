<template>
    <img :src="src" alt="image logo" :class="inputClass">
</template>

<script setup>
defineProps({
    src: {
        type: String,
        required: false,
        default: '/img/conagua.svg'
    },
    inputClass: {
        type: String,
        required: false,
        default: ''
    }
});
</script>
