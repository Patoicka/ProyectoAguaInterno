<script setup>
import { ref, onMounted } from "vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Button from "@/Components/Button.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiMapMarkerAlertOutline } from "@mdi/js";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import { Chart } from "chart.js/auto";
import axios from "axios";

const props = defineProps({
    name: 'IncidentGraph',
    title: { type: String, required: true },
    routeName: { type: String, required: true },
    incident: { type: Object, required: true, default: {} },
});
const chartData = ref({
    labels: [],
    datasets: []
});
const filtros = ref({
    years: [],
    types: [],
    statuses: [],
    cities: []
});
const availableFilters = ref({
    years: [],
    types: [],
    statuses: [],
    cities: []
});
const chartInstance = ref(null);

const filterMapping = {
    years: 'years',
    types: 'types',
    statuses: 'statuses',
    cities: 'cities'
};

const fetchChartData = (currentFilters = {}, wasFiltered = false) => {
    const mappedFilters = {};
    for (const key in currentFilters) {
        if (filterMapping[key]) {
            mappedFilters[filterMapping[key]] = currentFilters[key];
        }
    }

    axios.get(route('incident.showIncident'), { params: mappedFilters })
        .then(response => {
            chartData.value = response.data;
            updateChart(response.data, wasFiltered);
        })
        .catch(error => {
            console.error(error);
        });
};


const updateChart = (data, wasFiltered = false) => {
    const ctx = document.getElementById('chart')?.getContext('2d');
    if (!ctx) {
        console.error("No se pudo obtener el contexto del canvas");
        return;
    }

    if (!data || !data.labels || data.labels.length === 0 || !data.datasets || data.datasets.length === 0) {
        if (wasFiltered) {
            alert("No existen datos para ese filtro.");
            clearFilters();
        }
        return;
    }

    if (chartInstance.value) {
        chartInstance.value.destroy();
        chartInstance.value = null;
    }

    chartInstance.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels || [],
            datasets: data.datasets || []
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Gráfica de Incidencias',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Año',
                        font: {
                            size: 14
                        }
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Número de Incidencias',
                        font: {
                            size: 14
                        }
                    },
                    beginAtZero: true
                }
            }
        }
    });
};




const applyFilters = () => {
    fetchChartData(filtros.value, true);
};


const fetchAvailableFilters = async () => {
    try {
        const cachedCities = localStorage.getItem('availableCities');
        let cities = [];

        if (cachedCities) {
            cities = JSON.parse(cachedCities);
        } else {
            const response = await axios.get(route('incident.available-filters'));
            cities = response.data.cities;
            localStorage.setItem('availableCities', JSON.stringify(cities));
        }

        const response = await axios.get(route('incident.available-filters'));

        availableFilters.value = {
            years: response.data.years,
            types: response.data.types,
            statuses: response.data.statuses,
            cities: cities,
        };
    } catch (error) {
    }
};

onMounted(() => {
    fetchAvailableFilters();
    fetchChartData();
});

const clearFilters = () => {
    filtros.value = {
        years: [],
        types: [],
        statuses: [],
        cities: []
    };
    fetchChartData();
};

</script>

<template>
    <HeadLogo :title="Conagua" />

    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiMapMarkerAlertOutline" title="Dashboard - Incidencias" main />

        <CardBox>
            <div class="flex flex-col w-full h-full">
                <div class="flex flex-col sm:flex-row w-full sm:w-fit justify-between pb-4 sm:pb-0 sm:py-2 ">
                    <FormField class="order-2 sm:order-1" label="Años">
                        <FormControl placeholder="Seleccione Años" @change="applyFilters"
                            :options="availableFilters.years" v-model="filtros.years" />
                    </FormField>

                    <FormField class="order-2 sm:order-1 sm:mx-4" label="Tipo Incidencia">
                        <FormControl placeholder="Seleccione Tipo de Incidencia" @change="applyFilters"
                            :options="availableFilters.types" v-model="filtros.types" option-label="label"
                            option-value="value" />
                    </FormField>

                    <FormField class="order-2 sm:order-1" label="Estado Incidencia">
                        <FormControl placeholder="Seleccione un Estado" @change="applyFilters"
                            :options="availableFilters.statuses" v-model="filtros.statuses" />
                    </FormField>
                    <FormField class="order-2 sm:order-1 sm:mx-4" label="Municipio">
                        <FormControl placeholder="Seleccione Municipio" @change="applyFilters"
                            :options="availableFilters.cities" v-model="filtros.cities" />
                    </FormField>
                    <FormField class="order-2 sm:order-1 sm:mx-4" label="Acciones">
                        <PrimaryButton @click="clearFilters">
                            Limpiar Filtros
                        </PrimaryButton>
                        <Button @click="applyFilters">
                            Exportar
                        </Button>
                    </FormField>
                </div>

                <div> <canvas id="chart" height="50%" width="100%"></canvas></div>
            </div>
        </CardBox>
    </LayoutMain>
</template>