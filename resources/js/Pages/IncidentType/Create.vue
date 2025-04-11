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
    mdiMagnify,
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
import InputError from "@/Components/InputError.vue";
import axios from "axios"
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import HeadLogo from "@/Components/HeadLogo.vue";
import "vue-loading-overlay/dist/css/index.css";

const props = defineProps({
    name: "Create",
    title: { type: String, required: true, },
    routeName: { type: String, required: true, },
});

const form = useForm({
    name: null,
    description: null,
});

const saveForm = () => {
    form.post(route(`${props.routeName}store`));
};

</script>

<template>
    <HeadLogo :title="title" />
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

        <CardBox form @submit.prevent="saveForm">
            <FormField label="Nombre:" required :error="form.errors.name">
                <FormControl v-model="form.name" placeholder="Ingresa nombre" />
            </FormField>
            <FormField label="Descripción:" required :error="form.errors.description">
                <FormControl type="textarea" v-model="form.description" placeholder="Ingresa una descripción" height="h-36" />
            </FormField>
            <BaseDivider />
            <template #footer>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" />
                    <BaseButton :processing="form.processing" @click="saveForm" :icon="mdiContentSave" type="submit"
                        color="success" label="Guardar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
