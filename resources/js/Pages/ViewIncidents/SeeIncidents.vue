<script setup>
import { Head } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import MapIncidents from "@/Components/MapIncidents.vue";
import ChartIncidents from "@/Components/ChartIncidents.vue";
import axios from "axios";
import { computed, ref, onMounted } from "vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiBallotOutline } from "@mdi/js";

const props = defineProps({
  title: {
    type: String,
    required: true
  }
});

const selectedType = ref(null);
const incidents = ref([]);
const incidentTypes = ref({});
const municipalityColors = ref({});



const fetchIncidents = async () => {
  try {
    const response = await axios.get("/api/incidents");
    incidents.value = response.data.data || [];

    incidentTypes.value = incidents.value.reduce((types, incident) => {
      const typeName = incident.incident_type?.name;
      if (typeName && !types[typeName]) {
        types[typeName] = getRandomColor();
      }
      return types;
    }, {});

    municipalityColors.value = incidents.value.reduce((colors, incident) => {
      const municipio = incident.location?.neighborhood?.city?.name || 'Desconocido';
      if (!colors[municipio]) {
        colors[municipio] = getRandomColor();
      }
      return colors;
    }, {});

  } catch (error) {
    console.error("Error al cargar las incidencias:", error);
  }
};


const getRandomColor = () => {
  return "#" + Math.floor(Math.random() * 16777215).toString(16);
};

const filteredIncidents = computed(() => {
  if (!selectedType.value) return incidents.value;
  return incidents.value.filter((i) => i.incident_type?.name === selectedType.value);
});

const municipalityData = computed(() => {
  const counts = {};
  filteredIncidents.value.forEach((i) => {
    const municipio = i.location?.neighborhood?.city?.name || 'Desconocido';
    counts[municipio] = (counts[municipio] || 0) + 1;
  });
  return Object.values(counts);
});

const municipalityLabels = computed(() => {
  return [...new Set(filteredIncidents.value.map((i) => i.location?.neighborhood?.city?.name || 'Desconocido'))];
});

const municipalityChartColors = computed(() => {
  return municipalityLabels.value.map(label => municipalityColors.value[label] || '#cccccc');
});

onMounted(fetchIncidents);
</script>

<template>
  <LayoutMain>
    <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="title" main />

    <div class="container mx-auto p-4">

      <ChartIncidents 
        :data="municipalityData" 
        :labels="municipalityLabels" 
        :colors="municipalityChartColors" />

      <MapIncidents 
        class="mt-2"
        :incidents="filteredIncidents" 
        :incidentTypes="incidentTypes" 
        :selectedType="selectedType" 
        @update:selectedType="(val) => selectedType = val" />
    </div>
  </LayoutMain>
</template>
