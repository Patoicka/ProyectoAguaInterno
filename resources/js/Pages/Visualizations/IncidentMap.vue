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
import axios from "axios";
import wellknown from "wellknown";

let map;
let markersLayer = null;

const problematicsOptions = [
  "Todos", "Falta de agua", "Solicitud de pipa", "Fuga",
  "Falta de tapa en caja de valvula", "Brote de aguas negras", "Coladera sin tapa",
  "Socavón", "Encharcamiento", "Mala calidad", "Huachicol"
];

const statusOptions = [
  "Todos", "Enviada a revisión", "En proceso de atención", "Incidencia resuelta"
];

const incidenciasFiltradas = ref([]);
const todasLasIncidencias = ref([]);
const municipiosDisponibles = ref([]);

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
  const mexicoBounds = [
    [14.5, -118.5],
    [32.7, -86.7],
  ];

  map = L.map("map", {
    minZoom: 6,
    maxBounds: mexicoBounds,
    maxBoundsViscosity: 1.0,
  }).setView([19.35, -99.75], 9);

  L.tileLayer(
    "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
  ).addTo(map);

  fetch("assets/geojson/mexico.json")
    .then((res) => res.json())
    .then((data) => {
      data.forEach((item) => {
        const geometry = wellknown(item.poligono);
        if (!geometry) {
          console.warn("No se pudo convertir el polígono de:", item.municipio);
          return;
        }

        const feature = {
          type: "Feature",
          properties: {
            municipio: item.municipio,
          },
          geometry: geometry,
        };

        const layer = L.geoJSON(feature, {
          style: {
            color: "blue",
            weight: 1,
            fillOpacity: 0.1,
          },
        });

        layer.addTo(map);
      });
    })
    .catch((error) => {
      console.error("Error al cargar geojson:", error);
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

    const capitalizeFirstLetter = (string) => {
      return string
        .toLowerCase()
        .replace(/(?:^|\s)\S/g, (match) => match.toUpperCase());
    };

    if (i.estatus === "Enviada a revisión") {
      color = "bg-red-500";
    } else if (i.estatus === "En proceso de atención") {
      color = "bg-yellow-500";
    } else {
      color = "bg-green-500";
    }

    const tipo = capitalizeFirstLetter(i.tipo);
    const estatus = capitalizeFirstLetter(i.estatus);
    const municipio = capitalizeFirstLetter(i.municipio);
    const descripcion = capitalizeFirstLetter(i.descripcion);

    popUpContent = `
      <div class="text-sm font-medium text-gray-800 space-y-1">
        <div class="text-xl font-bold">
          ${tipo}
        </div>
        <div class="py-2 text-gray-600">
          <label class="font-semibold m-0">Lugar: </label>
          <label class="m-0">${municipio}</label>
        </div>
        <div class="pb-3 text-gray-600 text-justify">
          <label class="font-semibold m-0">Descripción: </label>
          <label class="m-0">${descripcion}</label>
        </div>
        <div class="p-2 rounded-lg ${color} text-white text-center mt-2">
          <strong>${estatus}</strong>
        </div>
    </div>
  `;

    L.marker([i.lat, i.lng], {
      icon: getIconByStatus(i.estatus, i.tipo),
    })
      .addTo(markersLayer)
      .bindPopup(popUpContent);
  });
};

const iconMap = {
  "Falta de agua": "lackofwater",
  "Solicitud de pipa": "waterpipe",
  "Fuga": "waterleak",
  "Falta de tapa en caja de valvula": "valve",
  "Brote de aguas negras": "sewage",
  "Coladera sin tapa": "colanderwithoutlid",
  "Socavón": "sinkhole",
  "Encharcamiento": "flood",
  "Mala calidad": "poorquality",
  "Huachicol": "huachicol",
};

const statusSuffixMap = {
  "Incidencia resuelta": "G",
  "En proceso de atención": "Y",
  "Enviada a revisión": "R",
};

const defaultIcons = {
  "Incidencia resuelta": "/icons/resolved.png",
  "En proceso de atención": "/icons/in_progress.png",
  "Enviada a revisión": "/icons/under_review.png",
};

const getIconByStatus = (status, type) => {
  const baseName = iconMap[type];
  const suffix = statusSuffixMap[status];

  const iconUrl = baseName
    ? `/icons/${baseName}${suffix}.png`
    : defaultIcons[status] || "/icons/default.png";

  return L.icon({
    iconUrl,
    iconSize: [30, 30],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
  });
};


const cargarIncidencias = async () => {
  try {
    const response = await axios.get("/api/map/incidents");
    const datos = response.data;

    todasLasIncidencias.value = datos.map((incidente) => {
      return {
        id: incidente.id,
        descripcion: incidente.description,
        tipo: incidente.incident_type || "Tipo desconocido",
        estatus: incidente.incident_status || "Estatus desconocido",
        municipio: incidente.municipality,
        lat: parseFloat(incidente.lat),
        lng: parseFloat(incidente.lng),
      };
    });

    const municipios = [
      ...new Set(
        todasLasIncidencias.value.map((i) => i.municipio).filter((m) => m)
      ),
    ];

    municipiosDisponibles.value = ["Todos", ...municipios.sort()];

    aplicarFiltros();
  } catch (error) {
    console.error("Error al cargar incidencias:", error);
  }
};

onMounted(() => {
  initMap();
  cargarIncidencias();
});
</script>

<template>
  <LayoutMain fullWidth>
    <SectionTitleLineWithButton :icon="mdiMapMarkerAlertOutline" title="Mapa de Incidencias en el Estado de México"
      main />
    <CardBox>
      <div class="flex flex-col w-full h-full">
        <div class="flex flex-col sm:flex-row w-full sm:w-fit justify-between pb-4 sm:pb-0">

          <FormField class="order-2 sm:order-1" label="Municipio">
            <FormControl placeholder="Elige un municipio" :options="municipiosDisponibles"
              v-model="filtros.municipality" />
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

        <div id="map" class="w-full h-[600px] rounded shadow"></div>
      </div>
    </CardBox>
  </LayoutMain>
</template>
