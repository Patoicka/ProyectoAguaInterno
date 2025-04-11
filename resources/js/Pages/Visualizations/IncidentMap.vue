<script setup>
import "leaflet/dist/leaflet.css";
import "leaflet/dist/images/marker-icon.png";
import "leaflet/dist/images/marker-shadow.png";
import L from "leaflet";
import { ref, onMounted, watch } from "vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiMapMarkerAlertOutline } from "@mdi/js";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";

let map;
let markersLayer = null;
const rows = defineModel('rows');

const municipalitysOptions = [
  "Todos", "Aculco", "Atlacomulco", "Chalco", "Chicoloapan", "Chimalhuacán",
  "Ecatepec", "Ixtapaluca", "La Paz", "Nezahualcóyotl", "Ozumba",
  "Texcoco", "Tlalnepantla de Baz", "Zacualpan"
];

const problematicsOptions = [
  "Todos", "Falta de agua", "Solicitud de pipa", "Fuga de agua", "Agua contaminada",
  "Falta tapa en caja de válvula", "Desbordamiento de aguas negras", "Coladera sin tapa",
  "Socavón / Hundimiento", "Inundación / Encharcamiento", "Drenaje tapado / coladera / Tubería",
  "Tomas Clandestinas"
];

const statusOptions = [
  "Todos", "Enviada a revisión", "En proceso de atención", "Incidencia resuelta"
];

// Datos de prueba, despues se consultara a la base de datos
const todasLasIncidencias = ref([
  { id: 1, tipo: "Fuga de agua", estatus: "Enviada a revisión", municipio: "Texcoco", lat: 19.5142, lng: -98.8995 },
  { id: 2, tipo: "Socavón / Hundimiento", estatus: "En proceso de atención", municipio: "Ecatepec", lat: 19.6016, lng: -99.0507 },
  { id: 3, tipo: "Falta de agua", estatus: "Incidencia resuelta", municipio: "Nezahualcóyotl", lat: 19.4004, lng: -98.9886 }
]);

const incidenciasFiltradas = ref([]);

// Inicializamos los filtros con valores por defecto
const filtros = ref({
  estado: "Estado de México",
  municipality: "Todos",
  problematic: "Todos",
  status: "Todos",
});

watch(
  filtros,
  () => {
    aplicarFiltros();
  },
  { deep: true }
);

const aplicarFiltros = () => {
  incidenciasFiltradas.value = todasLasIncidencias.value.filter((item) => {
    return (
      (filtros.value.municipality === "Todos" || item.municipio === filtros.value.municipality) &&
      (filtros.value.problematic === "Todos" || item.tipo === filtros.value.problematic) &&
      (filtros.value.status === "Todos" || item.estatus === filtros.value.status)
    );
  });

  renderMarkers();
};

const initMap = () => {
  map = L.map("map").setView([19.35, -99.75], 8);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
  }).addTo(map);

  fetch("assets/geojson/mexico.geojson")
    .then((Response) => Response.json())
    .then((data) => {
      L.geoJSON(data, {
        style: { color: "blue", weight: 1, opacity: 1, fillOpacity: 0.1 },
      }).addTo(map);
    });

  aplicarFiltros();
};

const renderMarkers = () => {
  if (markersLayer) {
    markersLayer.clearLayers();
  } else {
    markersLayer = L.layerGroup().addTo(map);
  }

  incidenciasFiltradas.value.forEach((i) => {
    let color;
    let popUpContent;

    if (i.estatus === "Enviada a revisión") {
      color = "red";
    } else if (i.estatus === "En proceso de atención") {
      color = "yellow";
    } else {
      color = "green";
    }

    popUpContent = `<strong>${i.tipo}</strong><br>${i.municipio}
    <br> <div style="background-color:${color}; padding:10px; border-radius:8px; display:flex; justify-content: center; align-items: center;">
                                    <strong>${i.estatus}</strong>
                                    </div>`;
    L.marker([i.lat, i.lng]).addTo(markersLayer).bindPopup(popUpContent);
  });
};

onMounted(() => {
  initMap();
});
</script>

<template>
  <HeadLogo title="Mapa de Incidencias" />

  <LayoutMain>
    <SectionTitleLineWithButton :icon="mdiMapMarkerAlertOutline" title="Mapa de Incidencias en el Estado de México"
      main />

    <CardBox>
      <div class="flex flex-col w-full h-full">
        <div class="flex flex-col sm:flex-row w-full sm:w-fit justify-between pb-4 sm:pb-0 sm:py-2 ">

          <FormField class="order-2 sm:order-1" label="Municipio">
            <FormControl @change="(e) => console.log(e.target.value)" placeholder="Elige un municipio"
              :options="municipalitysOptions" v-model="filtros.municipality" />
          </FormField>

          <FormField class="order-2 sm:order-1 sm:mx-4" label="Problematica">
            <FormControl @change="(e) => console.log(e.target.value)" placeholder="Elige una problemática"
              :options="problematicsOptions" v-model="filtros.problematic" />
          </FormField>

          <FormField class="order-2 sm:order-1" label="Estatus">
            <FormControl @change="(e) => console.log(e.target.value)" placeholder="Elige un estatus"
              :options="statusOptions" v-model="filtros.status" />
          </FormField>

        </div>

        <!-- Mapa -->
        <div id="map" class="w-full h-[600px] rounded shadow"></div>
      </div>
    </CardBox>
  </LayoutMain>
</template>
