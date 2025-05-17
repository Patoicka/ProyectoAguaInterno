<template>
    <CardBox isForm @submit.prevent="$emit('applyFilters', true)" class="mb-2 p-2">
        <div class="flex flex-col gap-2 lg:flex-row">
            <div class="flex flex-col sm:flex-row gap-2 md:basis-3/5">
                <FormField class="order-2 sm:order-1" label="Número de registros">
                    <FormControl @change="$emit('applyFilters', true)" placeholder="Elige un número"
                        :options="rowsPerPage" v-model="rows" />
                </FormField>
                <FormField class="order-1 sm:order-2 sm:grow" label="Búsqueda">
                    <FormControl type="search" ctrlKFocus :icon="mdiCarSearch" v-model="search"
                        placeholder="Ingresa un parámetro de búsqueda" />
                </FormField>
            </div>

            <slot />

            <div class="flex gap-2 h-12 sm:justify-center md:basis-2/5 lg:justify-evenly lg:mt-8">
                <BaseButton @click="$emit('clearFilters')" class="grow" :icon="mdiBroom" color="danger"
                    label="Limpiar filtros" />
                <BaseButton v-if="title" class="grow" :routeName="`${routeName}create`" :icon="mdiPlus" color="info"
                    :label="`Agregar ${title}`" />
            </div>
        </div>
    </CardBox>
</template>

<script setup>
import BaseButton from "@/Components/BaseButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormControl from "@/Components/FormControl.vue";
import FormField from "@/Components/FormField.vue";
import {
    mdiBroom,
    mdiPlus,
    mdiCarSearch
} from "@mdi/js";

const emits = defineEmits(['clearFilters', 'applyFilters']);

const props = defineProps({
    title: {
        type: String,
        required: false,
    },
    routeName: {
        type: String,
        required: true,
    },
});

const search = defineModel('search');
const rows = defineModel('rows');

const rowsPerPage = ["5", "10", "100", "1000"];

</script>