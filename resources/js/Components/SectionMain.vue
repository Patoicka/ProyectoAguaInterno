<script setup>
import { containerMaxW } from "@/config.js";

const props = defineProps({
  fullWidth: {
    type: Boolean,
    default: false,
  },
});
</script>

<template>
  <section class="p-1 md:p-6" :class="fullWidth ? 'w-full' : containerMaxW">
    <slot />
  </section>
</template>