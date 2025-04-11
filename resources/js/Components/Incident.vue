<script setup>
import CardBoxComponentEmpty from '@/Components/CardBoxComponentEmpty.vue';
import FormField from '@/Components/FormField.vue';
import LabelControl from '@/Components/LabelControl.vue';
import LocationForm from '@/Components/LocationForm.vue';
import { Tabs, Tab } from "flowbite-vue";
import { reactive } from 'vue';
import { defineProps, ref, computed, watch, provide } from "vue";
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Scrollbar, A11y } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import { dateToLocal } from '@/Hooks/useFormato';


const props = defineProps({
    incident: { type: Object, required: true, default: {} },
});

const activeTab = ref('incident');
const location = reactive({
    postal_code: props.incident.location.neighborhood.postal_code,
    street: props.incident.location.street,
    interior_number: props.incident.location.interior_number,
    exterior_number: props.incident.location.exterior_number,
    additional: props.incident.location.additional,
    references: props.incident.location.references,
    neighborhood_id: props.incident.location.neighborhood_id,
    city_id: props.incident.location.neighborhood.city_id,
    state_id: props.incident.location.neighborhood.city.state_id,
});

provide('form', location);

</script>

<template>
    <div id="alert-additional-content-1" :class="incident.incident_status.class" class="p-3 mt-3 mb-5 text-start"
        role="alert">
        <div class="flex items-center">
            <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <h3 class="md:text-lg text-sm font-medium">Incidencia con estatus "{{ incident.incident_status.name }}"</h3>
        </div>
        <div class="mt-2 md:text-sm text-xs">
            {{ incident.incident_status.description }}
        </div>
        <p class="md:text-sm text-xs">Ultima actualización: {{ dateToLocal(incident.updated_at, true) }} </p>
    </div>
    <Tabs v-model="activeTab" variant="pills" class="p-5">
        <Tab name="incident" title="Incidencia" :disabled="false">
            <FormField class="w-1/2" label="Folio:">
                <LabelControl :value="incident.unique_code" />
            </FormField>
            <FormField label="Tipo de incidencia:">
                <LabelControl :value="incident.incident_type.name" />
            </FormField>
            <FormField label="Descripción:">
                <LabelControl height="h-36" type="textarea" :value="incident.description" />
            </FormField>
        </Tab>
        <Tab name="location" title="Ubicación" :disabled="false">
            <LocationForm isShow />
        </Tab>
        <Tab name="images" title="Imagenes" :disabled="false">
            <swiper v-if="incident?.files.length" :modules="[Navigation, Pagination, Scrollbar, A11y]"
                :slides-per-view="1" :space-between="50" navigation :pagination="{ clickable: true }"
                :scrollbar="{ draggable: true }">
                <swiper-slide v-for="file in incident.files" :key="file.id">
                    <img :src="'/storage/' + file.path" alt="Vista previa" class="rounded-lg w-full">
                </swiper-slide>
                ...
            </swiper>
            <CardBoxComponentEmpty v-else />
        </Tab>
    </Tabs>
</template>