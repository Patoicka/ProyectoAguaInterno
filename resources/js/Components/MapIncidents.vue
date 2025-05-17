<script setup>
import { onMounted, ref, watch } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import CustomButton from "@/Components/CustomButton.vue";
import axios from "axios";

const props = defineProps({
    selectedType: String,
    incidents: {
        type: Array,
        default: () => [],
    },
    incidentTypes: {
        type: Object,
        default: () => ({}),
    },
});

const toggleCapas = () => {
    showCapas.value = !showCapas.value;
};

const emit = defineEmits(["update:selectedType"]);
const mapContainer = ref(null);
let map;
let markersLayer = L.layerGroup();
let edoMexLayer = null;
let municipiosLayer = null;
let coloniasLayer = null;

const showCapas = ref(false);
const showEdoMex = ref(true);
const showMunicipios = ref(false);
const showColonias = ref(false);
const capasMenu = ref(null);

const generateColor = (key) => {
    let hash = 0;
    for (let i = 0; i < key.length; i++) {
        hash = key.charCodeAt(i) + ((hash << 5) - hash);
    }
    return `hsl(${Math.abs(hash) % 360}, 80%, 50%)`;
};

const incidentColors = ref({});

watch(
    [() => props.incidentTypes, () => props.incidents],
    () => {
        Object.keys(props.incidentTypes).forEach((type) => {
            if (!incidentColors.value[type]) {
                incidentColors.value[type] = generateColor(type);
            }
        });

        props.incidents.forEach((incident) => {
            const typeName = incident.incident_type?.name;
            if (typeName && !incidentColors.value[typeName]) {
                incidentColors.value[typeName] = generateColor(typeName);
            }
        });
    },
    { immediate: true, deep: true }
);

const getIncidentColor = (type) => incidentColors.value[type] || "#333";

const updateMap = () => {
    markersLayer.clearLayers();

    props.incidents.forEach((incident) => {
        const lat = parseFloat(incident.location?.lat);
        const lng = parseFloat(incident.location?.lng);
        const typeName = incident.incident_type?.name;
        const municipio =
            incident.location?.neighborhood?.city?.name || "Desconocido";

        if (
            lat &&
            lng &&
            (!props.selectedType || typeName === props.selectedType)
        ) {
            const color = getIncidentColor(typeName);
            const marker = L.circleMarker([lat, lng], {
                color: color,
                fillColor: color,
                radius: 10,
                fillOpacity: 0.85,
                weight: 0.6,
            }).bindPopup(`
        <div class="text-center">
          <strong class="text-sm">${typeName}</strong>
          <p class="text-xs text-gray-600">${municipio}</p>
        </div>
      `);

            markersLayer.addLayer(marker);
            marker.bringToFront();
        }
    });
};

const loadEstadoDeMexico = async () => {
    try {
        const response = await axios.get(
            "https://raw.githubusercontent.com/PhantomInsights/mexico-geojson/refs/heads/main/2022/states/M%C3%A9xico.json"
        );

        if (edoMexLayer) map.removeLayer(edoMexLayer);
        edoMexLayer = L.geoJSON(response.data, {
            style: {
                color: "black",
                weight: 2,
                fillOpacity: 0.1,
                fillColor: "white",
                interactive: false,
            },
        });

        if (showEdoMex.value) edoMexLayer.addTo(map);
        map.fitBounds(edoMexLayer.getBounds());
    } catch (error) {
        console.error("Error al cargar GeoJSON del Estado de México:", error);
    }
};

const loadMunicipios = async () => {
    try {
        const response = await axios.get(
            "https://raw.githubusercontent.com/angelnmara/geojson/master/Municipios/MX-MEX.json"
        );

        if (municipiosLayer) map.removeLayer(municipiosLayer);
        municipiosLayer = L.geoJSON(response.data, {
            style: {
                color: "black",
                weight: 2,
                fillOpacity: 0.2,
                fillColor: "white",
                interactive: false,
            },
        });

        if (showMunicipios.value) municipiosLayer.addTo(map);
    } catch (error) {
        console.error("Error al cargar GeoJSON de Municipios:", error);
    }
};

const loadColonias = async () => {
    try {
        const response = await axios.get(
            "https://raw.githubusercontent.com/open-mexico/mexico-geojson/main/15-Mex.geojson"
        );

        if (coloniasLayer) map.removeLayer(coloniasLayer);
        coloniasLayer = L.geoJSON(response.data, {
            style: {
                color: "black",
                weight: 1,
                fillOpacity: 0.2,
                fillColor: "white",
                interactive: false,
            },
        });

        if (showColonias.value) coloniasLayer.addTo(map);
    } catch (error) {
        console.error("Error al cargar GeoJSON de Colonias:", error);
    }
};

const toggleLayer = (layer, show) => {
    if (layer) {
        if (show) layer.addTo(map);
        else map.removeLayer(layer);
    }
};

onMounted(() => {
    map = L.map(mapContainer.value).setView([19.35, -99.75], 9);

    L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
        {
            attribution: '&copy; <a href="https://carto.com/">CartoDB</a>',
        }
    ).addTo(map);

    loadEstadoDeMexico();
    loadMunicipios();
    loadColonias();
    markersLayer.addTo(map);
    updateMap();
});

watch(showEdoMex, () => toggleLayer(edoMexLayer, showEdoMex.value));
watch(showMunicipios, () => toggleLayer(municipiosLayer, showMunicipios.value));
watch(showColonias, () => toggleLayer(coloniasLayer, showColonias.value));

watch([() => props.selectedType, () => props.incidents], updateMap);
</script>

<template>
    <div
        class="relative bg-white dark:bg-gray-900 p-4 rounded-lg shadow-md border dark:border-none"
    >
        <h2 class="text-md font-semibold text-gray-900 dark:text-white mb-2">
            Filtrar Incidencias
        </h2>

        <div class="flex flex-wrap gap-2 justify-center mb-3">
            <CustomButton
                v-for="(color, type) in incidentTypes"
                :key="type"
                :label="type"
                :color="getIncidentColor(type)"
                :active="selectedType === type"
                @click="emit('update:selectedType', type)"
            />
        </div>

        <div
            ref="mapContainer"
            class="w-full h-[500px] rounded-lg shadow-md border dark:border-none"
        ></div>

        <!-- Menú de capas -->
        <div ref="capasMenu" class="absolute top-4 right-4 z-[9999]">
            <!-- Botón para mostrar/ocultar -->
            <button
                @click="toggleCapas"
                class="bg-white px-4 py-2 rounded-md shadow font-semibold text-sm hover:bg-gray-100 transition"
            >
                Capas
            </button>

            <!-- Contenido desplegable -->
            <div
                v-show="showCapas"
                class="mt-2 bg-white p-3 rounded-lg shadow-md z-[9999] absolute right-0"
            >
                <label class="block mb-1">
                    <input type="checkbox" v-model="showEdoMex" class="mr-2" />
                    Estado
                </label>
                <label class="block mb-1">
                    <input
                        type="checkbox"
                        v-model="showMunicipios"
                        class="mr-2"
                    />
                    Municipios
                </label>
                <label class="block">
                    <input
                        type="checkbox"
                        v-model="showColonias"
                        class="mr-2"
                    />
                    Colonias
                </label>
            </div>
        </div>
    </div>
</template>

