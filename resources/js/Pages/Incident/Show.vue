<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import {
    mdiBallotOutline,
    mdiInformation,
    mdiPlus,
    mdiPencil,
    mdiTrashCan,
    mdiContentSave,
    mdiClose,
    mdiMapSearch,
    mdiCheckAll,
    mdiReplyAll,
    mdiClipboardAccount,
} from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { defineProps, ref, computed, watch, provide } from "vue";
import { Link, Head, useForm } from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import InputError from "@/Components/InputError.vue";
import axios from "axios"
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import HeadLogo from "@/Components/HeadLogo.vue";
import { Tabs, Tab } from "flowbite-vue";
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Scrollbar, A11y } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import IncidentInfo from "@/Components/Incident.vue";

const props = defineProps({
    name: "Show",
    title: { type: String, required: true, },
    routeName: { type: String, required: true, },
    incident: { type: Object, required: true, default: {} },
});

const form = useForm({
    comments: props.incident.evidence?.comments ?? null,
    incident_id: props.incident.id,
    files: props.incident?.evidence?.files ?? [],
});

const saveForm = () => {
    form.post(route(`${props.routeName}evidenced`, props.incident.id));
};

const handleFileInput = (event) => {
    const newFiles = Array.from(event.target.files);

    if (form.files.length + newFiles.length > 5 || props.incident.evidence) {
        Swal.fire({
            title: "Limite de imagenes superado",
            text: "Maximo 5 imagenes",
            icon: "info",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Ok!",
        });
        return;
    }

    form.files.push(...newFiles);
};

const getImage = (index, path) => {
    if (path)
        return '/storage/' + path;
    return URL.createObjectURL(form.files[index])
}

const removeFile = (index) => {
    form.files.splice(index, 1); // Elimina el archivo del array
    form.errors[`files.${index}`] = null;
};

provide('form', form);

</script>

<template>
    <HeadLogo :title="title" />
    <div class="vl-parent">
        <loading v-model:active="form.processing" :can-cancel="false" :is-full-page="true" />
    </div>
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main>
            <Link :href="route(`${routeName}index`)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x"
                viewBox="0 0 16 16">
                <path
                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
            </svg>
            </Link>
        </SectionTitleLineWithButton>
        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.error }}
        </NotificationBar>
        <div class="md:grid md:grid-cols-5 gap-4">
            <CardBox class="col-span-3">
                <IncidentInfo :incident="incident" />
            </CardBox>
            <div class="col-span-2 h-full lg:relative">
                <CardBox isForm @submit.prevent="saveForm" class="lg:sticky lg:top-14 lg:overflow-y-auto">
                    <p class="font-semibold text-base mb-7"> {{ incident.evidence ? 'Evidencia' : 'Subir evidencia' }}
                    </p>
                    <FormField label="Comentarios:" :error="form.errors.comments" help="Ingresa un comentario">
                        <FormControl height="h-36" type="textarea" v-model="form.comments"
                            placeholder="Ingresa una Comentarios" />
                    </FormField>
                    <button v-if="!incident.evidence" class="relative mb-4 w-32 p-2 bg-blue-700 text-white rounded">
                        Subir imagenes
                        <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            accept=".jpg, .jpeg, .png" multiple @input="handleFileInput" type="file">
                    </button>
                    <swiper v-if="incident.evidence" :modules="[Navigation, Pagination, Scrollbar, A11y]"
                        :slides-per-view="1" :space-between="50" navigation :pagination="{ clickable: true }"
                        :scrollbar="{ draggable: true }">
                        <swiper-slide v-for="file in incident.evidence.files" :key="file.id">
                            <img :src="'/storage/' + file.path" alt="Vista previa" class="rounded-lg w-full">
                        </swiper-slide>
                        ...
                    </swiper>
                    <FormField v-else label="Imagenes seleccionadas" required :error="form.errors.files"
                        help="Selecciona al menos una imagen, maximo 5">
                        <table v-if="form.files.length" class="text-xs">
                            <thead>
                                <tr>
                                    <th />
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(file, index) in form.files" :key="index">
                                    <td data-label="">
                                        <img :src="getImage(index)" alt="Vista previa" class="rounded-lg w-28">
                                        <InputError :message="form.errors[`files?.${index}`]" />
                                    </td>
                                    <td data-label="Acciones">
                                        <BaseButton :icon="mdiTrashCan" color="danger" @click="removeFile(index)" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <CardBoxComponentEmpty v-else />
                    </FormField>

                    <template #footer>
                        <BaseButtons>
                            <BaseButton :disabled="incident.evidence ? true : false" :processing="form.processing"
                                @click="saveForm" :icon="mdiContentSave" type="submit" color="success"
                                label="Guardar" />
                        </BaseButtons>
                    </template>
                </CardBox>
            </div>
            <BaseButtons>
                <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" />
            </BaseButtons>
        </div>
    </LayoutMain>
</template>
    