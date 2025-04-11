<script setup>
import { Head } from "@inertiajs/vue3"

defineProps({
  title: {
    type: String,
    default: 'page'
  }
})

</script>

<template>
    <Head :title="title">
        <link rel="shortcut icon" type="image/png" href="/img/conagua.svg">
    </Head>
</template>
