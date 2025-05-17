<script setup>
import HeadLogo from '@/Components/HeadLogo.vue';
import LayoutWelcome from '@/Layouts/LayoutWelcome.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import BaseButtons from '@/Components/BaseButtons.vue';
import BaseButton from '@/Components/BaseButton.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import {
    mdiArrowRight,
    mdiQrcode,
    mdiInformation
} from "@mdi/js";
import CardBox from '@/Components/CardBox.vue';
import LabelControl from '@/Components/LabelControl.vue';
import NotFound from './Partials/NotFound.vue';
import BaseDivider from '@/Components/BaseDivider.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import { dateToLocal } from '@/Hooks/useFormato';
import IncidentInfo from "@/Components/Incident.vue";
import Working from './Partials/Working.vue';
import NotificationBar from '@/Components/NotificationBar.vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Scrollbar, A11y } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';

const props = defineProps({
    title: { type: String, required: true },
    incident: { type: Object, required: false, default: null }
});

</script>

<template>
    <HeadLogo :title="title" />
    <LayoutWelcome>
        <div class="max-w-7xl mx-auto py-16 px-1 sm:px-6 lg:py-20 lg:px-8">
            <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
                {{ $page.props.flash.success }}
            </NotificationBar>
            <div class="max-w-2xl lg:max-w-4xl mx-auto text-center mb-10">
                <h2 class="text-4xl text-gray-800 dark:text-blue-400 font-extrabold md:text-5xl">
                    Sistema de consulta de incidencias
                </h2>
                <p class="text-gray-600 dark:text-gray-300 text-sm md:text-xl mt-3">
                    Consulta el estado de tu incidencia en este portal. Nos aseguraremos de mantenerte informado en todo
                    momento. 😊
                </p>
            </div>
            <CardBox v-if="incident">
                <div class="md:grid md:grid-cols-5 gap-4">
                    <div class="col-span-3 md:p-5 border-r-2">
                        <IncidentInfo :incident="incident" />
                    </div>
                    <div class="col-span-2 h-full lg:relative">
                        <div class="lg:sticky lg:top-14 lg:overflow-y-auto md:p-5">
                            <span class="font-semibold text-xl dark:text-white">Evidencia capturada</span>
                            <div v-if="incident.evidence" class="mt-5">
                                <FormField label="Comentarios:">
                                    <LabelControl height="h-36" :value="incident.evidence.comments" />
                                </FormField>
                                <swiper v-if="incident.evidence.files.length > 0"
                                    :modules="[Navigation, Pagination, Scrollbar, A11y]" :slides-per-view="1"
                                    :space-between="50" navigation :pagination="{ clickable: true }"
                                    :scrollbar="{ draggable: true }">
                                    <swiper-slide v-for="file in incident.evidence.files" :key="file.id">
                                        <img :src="'/storage/' + file.path" alt="Vista previa"
                                            class="rounded-lg w-full">
                                    </swiper-slide>
                                    ...
                                </swiper>
                            </div>
                            <Working v-else />
                        </div>
                    </div>
                </div>
            </CardBox>

            <CardBox v-else class="flex items-center justify-center h-full mt-16">
                <div class="flex flex-col items-center py-10">
                    <NotFound label="Consultar de nuevo" route-name="welcome.search.incident" />
                </div>
            </CardBox>
        </div>

    </LayoutWelcome>
</template>
