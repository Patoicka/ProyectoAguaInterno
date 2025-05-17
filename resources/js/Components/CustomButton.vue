<script setup>
import { computed } from "vue";

const props = defineProps({
  label: String, // Texto del botón
  color: String, // Color de fondo del botón
  active: Boolean, // Estado activo/inactivo
});

const buttonClass = computed(() => [
  "px-4 py-2 text-sm font-semibold rounded-md transition-all duration-300",
  "shadow-sm hover:shadow-md",
  props.active 
    ? "bg-blue-600 text-white border-blue-700"
    : "bg-gray-200 text-gray-700 border-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600",
  "hover:scale-105",
]);

const buttonStyle = computed(() => ({
  border: `2px solid ${props.active ? props.color : "transparent"}`,
  minWidth: "120px",
  textAlign: "center",
}));
</script>

<template>
  <button :class="buttonClass" :style="buttonStyle">
    {{ label }}
  </button>
</template>
