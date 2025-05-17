<script setup>
import HeadLogo from '@/Components/HeadLogo.vue';
import HeroSection from './Partials/HeroSection.vue';
import LayoutWelcome from '@/Layouts/LayoutWelcome.vue';
import { Head, Link } from '@inertiajs/vue3';
const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    title: {
        type: String,
        required: true
    }
});

</script>

<template>
    <HeadLogo :title="title" />
    <LayoutWelcome>
        <HeroSection>
            <template #title>
                "Gestión de Incidentes de la Infraestructura de Agua Potable y Drenaje de la Comisión del Agua del Estado de México"
            </template>
            <template #description>
                Te damos la Bienvenida al sistema para el registro, monitoreo y seguimiento de los incidentes relacionados con el mal funcionamiento de la infraestructura de agua potable y drenaje del Estado de México. Los incidentes incluyen, entre otros, fugas de agua, baja calidad del serviico, interrupciones en el suministro, solicitudes de pipas, obstrucción de drenajes, inundaciones y falta de tapas en las cajas de válvula.
            </template>
        </HeroSection>

    </LayoutWelcome>
</template>
