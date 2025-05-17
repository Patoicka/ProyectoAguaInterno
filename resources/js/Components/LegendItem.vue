<script setup>
defineProps({
  label: String,
  color: String,
});
</script>

<template>
  <div class="flex items-center gap-2">
    <span class="w-4 h-4 rounded-full border" :style="{ backgroundColor: color }"></span>
    <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ label }}</span>
  </div>
</template>
