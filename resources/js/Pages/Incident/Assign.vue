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
import IncidentInfo from "@/Components/Incident.vue";

const props = defineProps({
    name: "Assign",
    title: { type: String, required: true, },
    routeName: { type: String, required: true, },
    incident: { type: Object, required: true, default: {} },
    users: { type: Object, required: true, default: {} },
});

const isLoading = ref(false);

const form = useForm({ reviewer_id: null, });

const saveForm = (id) => {
    Swal.fire({
        title: "¿Esta seguro?",
        text: "Esta acción asignara un revisor a la incidencia",
        icon: "info",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        cancelButtonColor: "#1C64F2",
        confirmButtonColor: "#059669",
        confirmButtonText: "Confirmar!",
    }).then((res) => {
        if (res.isConfirmed) {
            form.transform(data => ({
                ...data,
                reviewer_id: id
            })).post(route(`${props.routeName}assigned`, props.incident.id));
        }
    });
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

                <template #footer>
                    <BaseButtons>
                        <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" />
                    </BaseButtons>
                </template>
            </CardBox>
            <div class="col-span-2 h-full lg:relative">
                <CardBox isForm @submit.prevent="saveForm" class="lg:sticky lg:top-14 lg:overflow-y-auto">
                    <span class="font-semibold text-base">Asignar revisor</span>

                    <ul class="max-w-md divide-y divide-gray-200 dark:divide-gray-700 my-5">
                        <li v-for="user in users" class="pb-3 sm:pb-4">
                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                <div class="shrink-0">
                                    <img class="w-8 h-8 rounded-full" :src="user.file?.path ?? '/img/user.jpg'"
                                        alt="user image">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                        <strong>{{ user.roles?.[0]?.name }}</strong> {{ user.name }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                        {{ user.email }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center">
                                    <BaseButton :icon="mdiClipboardAccount" :disabled="incident.reviewer_id === user.id" @click="saveForm(user.id)"
                                        :label="incident.reviewer_id === user.id ? 'Asignado' : 'Asignar'"
                                        :color="incident.reviewer_id === user.id ? 'contrast' : 'success'" />
                                </div>
                            </div>
                        </li>
                    </ul>
                </CardBox>
            </div>
        </div>
    </LayoutMain>
</template>
