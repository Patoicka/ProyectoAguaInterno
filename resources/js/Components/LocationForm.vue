<script setup>
import BaseButton from "./BaseButton.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import Swal from "sweetalert2";

import axios from "axios";
import { inject, onMounted, ref } from 'vue';
import { mdiMagnify } from "@mdi/js";

const props = defineProps({
    isShow: { type: Boolean, default: false },
    streetIsRequired: { type: Boolean, default: false },
    hasNumbers: { type: Boolean, default: true },
    exteriorIsRequired: { type: Boolean, default: false },
    referencesIsRequired: { type: Boolean, default: false }
})

const isLoading = ref(false);
const form = inject('form');
const cities = ref([]);
const neighborhoods = ref([]);
const routeNameLocation = 'location.';

const states = [
    { id: 1, name: "Aguascalientes" },
    { id: 2, name: "Baja California" },
    { id: 3, name: "Baja California Sur" },
    { id: 4, name: "Campeche" },
    { id: 5, name: "Chiapas" },
    { id: 6, name: "Chihuahua" },
    { id: 7, name: "Coahuila" },
    { id: 8, name: "Colima" },
    { id: 9, name: "Ciudad de México" },
    { id: 10, name: "Durango" },
    { id: 11, name: "Guanajuato" },
    { id: 12, name: "Guerrero" },
    { id: 13, name: "Hidalgo" },
    { id: 14, name: "Jalisco" },
    { id: 15, name: "México" },
    { id: 16, name: "Michoacán" },
    { id: 17, name: "Morelos" },
    { id: 18, name: "Nayarit" },
    { id: 19, name: "Nuevo León" },
    { id: 20, name: "Oaxaca" },
    { id: 21, name: "Puebla" },
    { id: 22, name: "Querétaro" },
    { id: 23, name: "Quintana Roo" },
    { id: 24, name: "San Luis Potosí" },
    { id: 25, name: "Sinaloa" },
    { id: 26, name: "Sonora" },
    { id: 27, name: "Tabasco" },
    { id: 28, name: "Tamaulipas" },
    { id: 29, name: "Tlaxcala" },
    { id: 30, name: "Veracruz" },
    { id: 31, name: "Yucatán" },
    { id: 32, name: "Zacatecas" }
];

const cleanForm = () => {
    form.postal_code = null;
    form.state_id = null;
    form.city_id = null;
    form.neighborhood_id = null;
    form.street = null;
    form.interior_number = null;
    form.exterior_number = null;
    form.additional = null;
    form.references = null;
    cities.value = [];
    neighborhoods.value = [];
};

const getLocationByPostalCode = async (postalCode) => {
    isLoading.value = true;
    try {
        const response = await axios.get(route(`${routeNameLocation}getLocationByPostalCode`, postalCode));
        if (response.status === 200) {
            const data = response.data;
            cities.value = data.cities ?? null;
            neighborhoods.value = data.neighborhoods ?? null;

            form.state_id = form.state_id ?? data.state_id ?? null;
            form.city_id = form.city_id ?? data.cities[0]?.id ?? null;
            form.neighborhood_id = form.neighborhood_id ?? data.neighborhoods[0]?.id ?? null;
        }
    } catch (error) {
        Swal.fire({
            title: 'UPS!',
            text: 'Sin resultados, verifica el código postal',
            icon: 'info',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok',
        });
        cleanForm();
        return;
    } finally {
        isLoading.value = false;
    }
};

const getLocationByState = (stateId) => {
    neighborhoods.value = null;
    form.neighborhood_id = null;
    form.postal_code = null;
    axios.get(route(`${routeNameLocation}getLocationByState`, stateId))
        .then(response => {
            if (response.status === 200) {
                const data = response.data
                cities.value = data.cities;
                form.city_id = data.cities[0]?.id ?? null;
            }
        })
        .catch(error => {
            Swal.fire({
                title: '¡Error!',
                text: 'Error al obtener los municipios, intente nuevamente más tarde',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok',
            });
        });
};

const getLocationByCity = (cityId) => {
    axios.get(route(`${routeNameLocation}getLocationByCity`, cityId))
        .then(response => {
            if (response.status === 200) {
                const data = response.data
                neighborhoods.value = data.neighborhoods;
                form.neighborhood_id = data.neighborhoods[0]?.id ?? null;
                form.postal_code = data.neighborhoods[0]?.postal_code ?? null;
            }
        })
        .catch(error => {
            Swal.fire({
                title: '¡Error!',
                text: 'Error al obtener las colonias, intente nuevamente más tarde',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok',
            });
        });
};

const getLocationByNeighborhood = (neighborhoodId) => {
    axios.get(route(`${routeNameLocation}getLocationByNeighborhood`, neighborhoodId))
        .then(response => {
            if (response.status === 200) {
                const data = response.data
                form.postal_code = data.postalCode;
            }
        })
        .catch(error => {
            Swal.fire({
                title: '¡Error!',
                text: 'Error al obtener el código postal, intente nuevamente más tarde',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok',
            });
        });
};

const convertToUpperCase = (input) => {
    form[input] = form[input].toUpperCase();
};

defineExpose({ getLocationByPostalCode });

onMounted(() => {
    if (form.postal_code) {
        getLocationByPostalCode(form.postal_code);
    }
});
</script>

<template>
    <div class="mb-5 flex flex-col lg:flex-row">
        <div class="">
            <FormField label="Código postal:" :error="form?.errors?.postal_code">
                <FormControl :disabled="isShow" placeholder="Código postal" type="number"
                    @keyup.enter="getLocationByPostalCode(form.postal_code)" v-model="form.postal_code" />
            </FormField>
        </div>

        <div class="mt-5 flex gap-1 lg:flex-row lg:mt-8 lg:ml-2">
            <BaseButton class="w-full lg:w-auto h-12" :disabled="isShow" label="Buscar" color="info" :icon="mdiMagnify"
                @click="getLocationByPostalCode(form.postal_code)" />
            <BaseButton :disabled="isShow" class="w-full lg:w-auto h-12" label="Limpiar" color="contrast" @click="cleanForm()" />
        </div>
    </div>

    <div class="mb-5">
        <FormField label="Estado:" :error="form?.errors?.state_id">
            <FormControl :disabled="isShow" v-model="form.state_id" :options="states" @change="getLocationByState(form.state_id)" />
        </FormField>
    </div>

    <div class="mb-5">
        <FormField label="Municipio:" :error="form?.errors?.city_id">
            <FormControl :disabled="isShow" v-model="form.city_id" :options="cities" @change="getLocationByCity(form.city_id)" />
        </FormField>
    </div>

    <FormField required label="Colonia:" :error="form?.errors?.neighborhood_id">
        <FormControl :disabled="isShow" type="select" v-model="form.neighborhood_id" :options="neighborhoods"
            @change="getLocationByNeighborhood(form.neighborhood_id)" />
    </FormField>

    <FormField required label="Calle:" :error="form?.errors?.street" :required="streetIsRequired">
        <FormControl :disabled="isShow" @input="convertToUpperCase('street')" v-model="form.street" placeholder="Calle" />
    </FormField>

    <div v-if="hasNumbers" class="md:flex mb-5 md:space-x-4">
        <div class="md:w-1/2 max-lg:mb-5">
            <FormField required label="Número interior:" :error="form?.errors?.interior_number">
                <FormControl :disabled="isShow" v-model="form.interior_number" placeholder="Número interior" maxlength="50" />
            </FormField>
        </div>
        <div class="md:w-1/2">
            <FormField required label="Número exterior:" :error="form?.errors?.exterior_number">
                <FormControl :disabled="isShow" v-model="form.exterior_number" placeholder="Número exterior" maxlength="50" />
            </FormField>
        </div>
    </div>
    <FormField required label="Manzana/Lote/Solar:" :error="form?.errors?.additional">
        <FormControl :disabled="isShow" height="h-14" @input="convertToUpperCase('additional')" v-model="form.additional"
            placeholder="Ingresa Manzana/Lote/Solar" />
    </FormField>
    <FormField required label="Referencias:" :error="form?.errors?.references" :required="referencesIsRequired">
        <FormControl :disabled="isShow" type="textarea" height="h-24" @input="convertToUpperCase('references')" v-model="form.references"
            placeholder="Descripción del sitio" />
    </FormField>

    <div class="vl-parent">
        <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
    </div>
</template>