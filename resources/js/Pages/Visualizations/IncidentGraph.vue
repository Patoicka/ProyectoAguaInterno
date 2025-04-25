<script setup>
import { ref, onMounted, watch, render } from "vue";
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
    neighborhoods: []
});
const availableFilters = ref({
    years: [],
    types: [],
    statuses: [],
    neighborhoods: []
});
const chartInstance = ref(null);

const filterMapping = {
    years: 'years',
    types: 'types',
    statuses: 'statuses',
    neighborhoods: 'neighborhoods',
};

const fetchChartData = (currentFilters = {}) => {
    const mappedFilters = {};
    for (const key in currentFilters) {
        if (filterMapping[key]) {
            mappedFilters[filterMapping[key]] = currentFilters[key];
        }
    }
    axios.get('/showIncident', { params: mappedFilters })
        .then(response => {
            // 1. Verificar la estructura de la respuesta:
            console.log("Respuesta de fetchChartData:", response.data);

            // 2. Asignar directamente los datos recibidos al chartData.value
            chartData.value = response.data;
            updateChart(response.data); // Pasa la respuesta directamente a updateChart
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
        });
};

const updateChart = (data) => { // Recibe los datos como argumento
    if (chartInstance.value) {
        chartInstance.value.destroy();
    }
    const ctx = document.getElementById('chart').getContext('2d');
    if (!ctx) {
        console.error("No se pudo obtener el contexto del canvas");
        return;
    }

    chartInstance.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,  // Usa las etiquetas del controlador
            datasets: data.datasets // Usa los datasets del controlador
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
                        text: 'Periodo',
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
    fetchChartData(filtros.value);
};

const fetchAvailableFilters = () => {
  axios.get('/available-incident-filters')
    .then(response => {
      availableFilters.value = response.data;
      if (response.data.years) {
        availableFilters.value.years = response.data.years.map(year => ({
          label: year.toString(),
          value: year
        }));
      }
      if (response.data.types) {
        availableFilters.value.types = response.data.types.map(type => ({
          label: type,
          value: type
        }));
      }
      if (response.data.statuses) {
        availableFilters.value.statuses = response.data.statuses.map(status => ({
          label: status,
          value: status
        }));
      }
      if (response.data.neighborhoods) {
        availableFilters.value.neighborhoods = response.data.neighborhoods.map(neighborhood => ({
          label: neighborhood,
          value: neighborhood
        }));
      }
    })
    .catch(error => {
      console.error('Error fetching available filters:', error);
    });
};


onMounted(() => {
  fetchAvailableFilters();  // Primero obtener los filtros disponibles
  fetchChartData(); // Luego obtener los datos del gráfico inicial
});



/* async function fetchChartData(){
    const charData = ref(null);
    try{
        const response = await axios.get('/showIncident');
        renderChart(response.data);
    }catch (error) {
        console.error("Error fetching chart data:", error);
    }
}

function renderChart(data) {
    const ctx = document.getElementById('chart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: data
    });
} */


</script>

<template>
    <HeadLogo :title="title" />

    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiMapMarkerAlertOutline" title="tittle" main />

        <CardBox>
            <div class="flex flex-col w-full h-full">
                <div class="flex flex-col sm:flex-row w-full sm:w-fit justify-between pb-4 sm:pb-0 sm:py-2 ">

                    <FormField class="order-2 sm:order-1" label="Años">
                        <FormControl placeholder="Seleccione Años" :options="availableFilters.years"
                            v-model="filtros.years" multiple />
                    </FormField>

                    <FormField class="order-2 sm:order-1 sm:mx-4" label="Tipos">
                        <FormControl placeholder="Seleccione Tipos" :options="availableFilters.types"
                            v-model="filtros.types" multiple />
                    </FormField>

                    <FormField class="order-2 sm:order-1" label="Estados">
                        <FormControl placeholder="Seleccione Estados" :options="availableFilters.statuses"
                            v-model="filtros.statuses" multiple />
                    </FormField>
                    <FormField class="order-2 sm:order-1 sm:mx-4" label="Colonias">
                        <FormControl placeholder="Seleccione Colonias" :options="availableFilters.neighborhoods"
                            v-model="filtros.neighborhoods" multiple />
                    </FormField>

                </div>
                <button @click="applyFilters">Aplicar Filtros</button>
                <!-- Mapa -->
                <div> <canvas id="chart"></canvas></div>
            </div>
        </CardBox>
    </LayoutMain>
</template>