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
    mdiStar,
} from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { defineProps, ref, computed, watch, provide } from "vue";
import { Link, Head, useForm } from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import BaseIcon from "@/Components/BaseIcon.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import InputError from "@/Components/InputError.vue";
import axios from "axios"
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import HeadLogo from "@/Components/HeadLogo.vue";
import { Tabs, Tab } from "flowbite-vue";
import LocationForm from "@/Components/LocationForm.vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import LayoutWelcome from "@/Layouts/LayoutWelcome.vue";

const props = defineProps({
    name: "Create",
    title: { type: String, required: true, },
    routeName: { type: String, required: true, },
    incidentTypes: { type: Object, required: true, default: {} },
});

const activeTab = ref('general');
const form = useForm({
    description: null,
    report_id: null,
    location_id: null,
    incident_type_id: null,
    incident_status_id: null,
    files: [],

    // reportador
    names: null,
    first_surname: null,
    second_surname: null,
    number: null,
    contact_email: null,

    // location
    street: null,
    interior_number: null,
    exterior_number: null,
    additional: null,
    references: null,
    neighborhood_id: null,
    city_id: null,
    state_id: null,
});

const isLoading = ref(false);
const saveForm = () => {
    form.post(route(`${props.routeName}store.incidet`), {
        onError: (errors) => {
            isLoading.value = false
            Swal.fire({
                title: 'UPS!',
                text: 'Al parecer hay campos inválidos, por favor revísalos con cuidado.',
                icon: 'info',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok',
            })
        }
    });
};

const handleFileInput = (event) => {
    isLoading.value = true;
    const newFiles = Array.from(event.target.files);

    if (form.files.length + newFiles.length > 5) {
        isLoading.value = false;
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
    isLoading.value = false;
};

const getImage = (index) => {
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
    <LayoutWelcome>
        <div data-aos="fade-down" class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-20 lg:px-8">
            <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
                {{ $page.props.flash.error }}
            </NotificationBar>

            <div>
                <input v-model="form['token']" type="hidden" id="g-recaptcha-response" name="token" />
                <InputError :message="form.errors.token" class="mt-2" />
            </div>

            <div class="max-w-2xl lg:max-w-4xl mx-auto text-center mb-10">
                <h2 class="text-4xl text-gray-800 dark:text-blue-400 font-extrabold md:text-5xl">
                    {{ title }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-base mt-3">
                    La Comisión Nacional del Agua ha creado este espacio para que puedas reportar incidencias en tu
                    comunidad. Queremos escucharte y comprometernos a brindarte una solución a través de este medio.
                <p class="justify-center flex">
                    <strong>¡Tu participación es clave para mejorar nuestro entorno</strong>
                    <BaseIcon :path="mdiStar" class="text-yellow-600 dark:text-yellow-300" />
                </p>
                </p>
            </div>
            <CardBox>
                <Tabs v-model="activeTab" variant="pills" class="p-5">
                    <Tab name="general" title="Datos generales del solicitante">
                        <FormField label="Tipo de incidencia:" required :error="form.errors.incident_type_id">
                            <FormControl v-model="form.incident_type_id" :options="incidentTypes" />
                        </FormField>
                        <FormField label="Descripción:" required :error="form.errors.description"
                            help="Ingresa una descripción de los hechos">
                            <FormControl height="h-36" type="textarea" v-model="form.description"
                                placeholder="Ingresa una descripción" />
                        </FormField>
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3 mb-5">
                            <FormField label="Nombre(s):" required :error="form.errors.names">
                                <FormControl v-model="form.names" placeholder="Ingresa tu nombre(s)" />
                            </FormField>
                            <FormField label="Apellido paterno:" :error="form.errors.first_surname">
                                <FormControl v-model="form.first_surname" placeholder="Ingresa tu apellido paterno" />
                            </FormField>
                            <FormField label="Apellido materno:" :error="form.errors.second_surname">
                                <FormControl v-model="form.second_surname" placeholder="Ingresa tu apellido materno" />
                            </FormField>
                        </div>
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                            <FormField label="Teléfono:" required :error="form.errors.number">
                                <FormControl v-model="form.number" placeholder="Ingresa tu apellido materno" />
                            </FormField>
                            <FormField required label="Correo electrónico:" :error="form.errors.contact_email">
                                <FormControl v-model="form.contact_email" placeholder="Ingresa correo electrónico" />
                            </FormField>
                        </div>

                    </Tab>
                    <Tab name="location" title="Ubicación">
                        <LocationForm />
                    </Tab>
                    <Tab name="images" title="Cargar imagenes">
                        <button class="relative mb-4 w-32 p-2 bg-blue-700 text-white rounded">
                            Subir imagenes
                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept=".jpg, .jpeg, .png" multiple @input="handleFileInput" type="file">
                        </button>
                        <FormField label="Imagenes seleccionadas" required :error="form.errors.files"
                            help="Selecciona al menos una imagen, maximo 5">
                            <table v-if="form.files.length" class="text-xs dark:text-white">
                                <thead>
                                    <tr>
                                        <th />
                                        <th>Nombre</th>
                                        <th>Tamaño</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(file, index) in form.files" :key="index">
                                        <td data-label="">
                                            <img :src="getImage(index)" alt="Vista previa" class="rounded-lg w-28">
                                            <InputError :message="form.errors[`files.${index}`]" />
                                        </td>
                                        <td data-label="Nombre">
                                            {{ file.name }}
                                        </td>
                                        <td data-label="Tamaño">
                                            {{ (file?.size / 1000).toFixed(2) }} KB
                                        </td>
                                        <td data-label="Acciones">
                                            <BaseButton :icon="mdiTrashCan" color="danger" @click="removeFile(index)" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <CardBoxComponentEmpty v-else />
                        </FormField>
                    </Tab>
                </Tabs>

                <BaseDivider />
                <template #footer>
                    <BaseButtons>
                        <!-- <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" /> -->
                        <BaseButton :processing="form.processing" @click="saveForm" :icon="mdiContentSave" type="submit"
                            color="success" label="Guardar" />
                    </BaseButtons>
                </template>
            </CardBox>
        </div>
    </LayoutWelcome>
</template>
