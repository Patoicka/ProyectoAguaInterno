<script setup>
import { ref, onMounted } from "vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Button     from "@/Components/Button.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox    from "@/Components/CardBox.vue";
import FormField  from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import { Chart } from "chart.js/auto";
import axios from "axios";
import {mdiFilePdfBox} from "@mdi/js";
import { icon } from "leaflet";

const props = defineProps({
  routeName: { type: String, default: "" },         // ← ya no required
  name:      { type: String, default: "IncidentGraph" },
  title:     { type: String, default: "Dashboard – Incidencias" },
  incident:  { type: Object,  default: () => ({}) },
});

/* ── 2. estado reactivo ───────────────────────────────────── */
const chartData = ref({ labels: [], datasets: [] });
const filtros   = ref({ years: [], types: [], statuses: [], cities: [] });
const availableFilters = ref({ years: [], types: [], statuses: [], cities: [] });
const chartInstance   = ref(null);

/* ── 3. helpers ───────────────────────────────────────────── */
const filterMapping = { years: "years", types: "types", statuses: "statuses", cities: "cities" };

const safeDestroy = () => {
  if (chartInstance.value) {
    chartInstance.value.stop();
    chartInstance.value.destroy();
    chartInstance.value = null;
  }
};

const config = (data) => ({
  type: "bar",
  data: { labels: data.labels, datasets: data.datasets },
  options: {
    animation: false,                         // ← sin animaciones, adiós ctx.save
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      title: { display: true, text: "Gráfica de Incidencias", font: { size: 16 } },
      legend: { position: "bottom" },
    },
    scales: {
      x: { title: { display: true, text: "Año", font: { size: 14 } } },
      y: {
        beginAtZero: true,
        title: { display: true, text: "Número de Incidencias", font: { size: 14 } },
      },
    },
  },
});

/* ── 4. lógica de la gráfica ─────────────────────────────── */
const fetchChartData = (currentFilters = {}, wasFiltered = false) => {
  const mapped = {};
  Object.keys(currentFilters).forEach(k => {
    if (filterMapping[k]) mapped[filterMapping[k]] = currentFilters[k];
  });

  axios.get(route("incident.showIncident"), { params: mapped })
       .then(({ data }) => updateChart(data, wasFiltered))
       .catch(console.error);
};

const updateChart = (data, wasFiltered = false) => {
  const ctx = document.getElementById("chart")?.getContext("2d");
  if (!ctx) return;

  const noData =
    !data ||
    !data.labels?.length ||
    !data.datasets?.length ||
    data.datasets.every(ds => ds.data.every(v => v === 0));

  if (noData) {
    safeDestroy();
    if (wasFiltered) alert("Sin datos para los filtros seleccionados.");
    return;
  }

  safeDestroy();
  chartInstance.value = new Chart(ctx, config(data));
};

/* ── 5. filtros y carga inicial ───────────────────────────── */
const applyFilters = () => fetchChartData(filtros.value, true);

const fetchAvailableFilters = async () => {
  const cached = localStorage.getItem("availableCities");
  let cities   = cached ? JSON.parse(cached) : [];

  const { data } = await axios.get(route("incident.available-filters"));
  if (!cached) localStorage.setItem("availableCities", JSON.stringify(data.cities));

  availableFilters.value = { years: data.years, types: data.types, statuses: data.statuses, cities: data.cities };
};

onMounted(() => {
  fetchAvailableFilters();
  fetchChartData();
});

const clearFilters = () => {
  filtros.value = { years: [], types: [], statuses: [], cities: [] };
  fetchChartData();
};

/* ── 6. exportar PDF ───────────────────────────────────────── */
const exportPdf = () => {
    if (!filtros.value.years || filtros.value.years.length === 0) {
        alert("Por favor, seleccione un año para exportar el PDF.");
        return;
    }
    const year = filtros.value.years;
    const url = route('incident.exportPdf')+`?anio=${year}`;
    window.open(url, '_blank');
};
</script>

<template>
  <HeadLogo title="Conagua" />

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

                        <Button @click="exportPdf" :icon="mdiFilePdfBox" class="ml-2" icon="mdiFilePdfBox" color="redWhite">
                            Exportar PDF
                        </Button>

                    </FormField>
                </div>

                <div> <canvas id="chart" height="50%" width="100%"></canvas></div>
            </div>
        </CardBox>
    </LayoutMain>
</template>
