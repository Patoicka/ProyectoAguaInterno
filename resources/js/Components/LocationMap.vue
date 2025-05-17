<script setup>
import { onMounted, ref, defineProps, defineEmits,watch } from "vue";
import "leaflet/dist/leaflet.css";
import L from "leaflet";
import axios from "axios";

import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

const defaultIcon = L.icon({
  iconUrl: markerIcon,
  iconRetinaUrl: markerIcon2x,
  shadowUrl: markerShadow,
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

// Sobrescribe el ícono por defecto de Leaflet
L.Marker.prototype.options.icon = defaultIcon;
const emit = defineEmits(["close", "location-selected"]);
const props = defineProps({
  postalCode: Number,
  state: Number,
  municipality: Number,
  neighborhood: Number,
});

const latitude = ref(null);
const longitude = ref(null);
const marker = ref(null);
let map;
let searchTimeout = null;
let abortController = null;
const mexBounds = L.latLngBounds(L.latLng(18.37, -100.39), L.latLng(20.22, -98.58));
let edoMexLayer = null;
const searchQuery = ref("");
const searchResults = ref([]);
const searchBounds = ref(null);
let municipiosLayer = null;

async function locateByPostalCode() {
  if (!props.postalCode) {
    console.warn("No hay código postal para buscar.");
    return;
  }

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&country=Mexico&postalcode=${props.postalCode}`
    );
    const data = await response.json();

    if (data.length > 0) {
      const { lat, lon, boundingbox } = data[0];
      latitude.value = parseFloat(lat);
      longitude.value = parseFloat(lon);
      searchBounds.value = [
        [parseFloat(boundingbox[0]), parseFloat(boundingbox[2])], // Suroeste
        [parseFloat(boundingbox[1]), parseFloat(boundingbox[3])]  // Noreste
      ];
      map.setView([latitude.value, longitude.value], 16);
      placeMarker(latitude.value, longitude.value, "Confirma tu ubicación ");
    } else {
      console.warn("No se encontraron coordenadas para el código postal.");
    }
  } catch (error) {
    console.error("Error buscando por código postal:", error);
  }
}

async function onMapClick(e) {
  const { lat, lng } = e.latlng;
  latitude.value = lat;
  longitude.value = lng;

  placeMarker(lat, lng, "Ubicación seleccionada");
  const addressData = await reverseGeocode(lat, lng);
  emit("location-selected", { lat, lng, street: addressData?.road || "", postal_code: addressData?.postcode || "" });
}
// Función para buscar direcciones
async function searchAddress() {
  clearTimeout(searchTimeout);

  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    return;
  }

  searchTimeout = setTimeout(async () => {
    if (!searchBounds.value) return;

    if (abortController) {
      abortController.abort();
    }

    abortController = new AbortController();

    const query = encodeURIComponent(`${searchQuery.value}, Estado de México, Mexico`);
    const [southWest, northEast] = searchBounds.value;
    const viewbox = `${northEast[1]},${northEast[0]},${southWest[1]},${southWest[0]}`;

    try {
      const response = await fetch(
        `https://nominatim.openstreetmap.org/search?format=json&q=${query}&bounded=1&viewbox=${viewbox}`,
        { signal: abortController.signal }
      );
      const data = await response.json();
      searchResults.value = data.map((result) => ({
        place_id: result.place_id,
        display_name: result.display_name,
        lat: parseFloat(result.lat),
        lon: parseFloat(result.lon),
      }));
    } catch (error) {
      if (error.name !== "AbortError") {
        console.error("Error en la búsqueda de dirección:", error);
      }
    }
  }, 300);
}
// Función para seleccionar una dirección de la lista de resultados
function selectAddress(result) {
  latitude.value = result.lat;
  longitude.value = result.lon;
  map.setView([latitude.value, longitude.value], 16);
  placeMarker(latitude.value, longitude.value, "Ubicación seleccionada");
  searchResults.value = [];
}
// Función para geocodificación inversa
// Esta función toma las coordenadas y devuelve la dirección
async function reverseGeocode(lat, lng) {
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
    if (!response.ok) throw new Error("Error en la solicitud");
    const data = await response.json();
    return data.address || null;
  } catch (error) {
    console.error("Error en la geocodificación inversa:", error);
    return null;
  }
}

function placeMarker(lat, lng, popupText) {
  if (marker.value) {
    marker.value.setLatLng([lat, lng]).setPopupContent(popupText).openPopup();
  } else {
    marker.value = L.marker([lat, lng], { draggable: true })
      .addTo(map)
      .bindPopup(popupText)
      .openPopup();
  }
}

async function loadEstadoDeMexico() {
  try {
    const response = await axios.get(
      "https://raw.githubusercontent.com/PhantomInsights/mexico-geojson/refs/heads/main/2022/states/M%C3%A9xico.json"
    );
    if (edoMexLayer) {
      map.removeLayer(edoMexLayer);
    }
    edoMexLayer = L.geoJSON(response.data, {
      style: {
        color: "black",
        weight: 2,
        fillOpacity: 0,
      },
    }).addTo(map);
    map.fitBounds(edoMexLayer.getBounds());
  } catch (error) {
    console.error("Error al cargar el GeoJSON del Estado de México:", error);
  }
}

onMounted(() => {
  map = L.map("map", {
    maxBounds: mexBounds,
    maxBoundsViscosity: 1.0,
  }).setView([19.35, -99.75], 10);

  L.tileLayer("https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png", {
    attribution: "&copy; <a href='https://carto.com/'>CartoDB</a>",
  }).addTo(map);

  loadEstadoDeMexico();
  map.on("click", onMapClick);

  if (props.postalCode) {
    locateByPostalCode();
  }
});

//  Redireccionar el mapa automáticamente cuando cambia el código postal
watch(() => props.postalCode, (newPostalCode) => {
  // Limpiar marcador y resultados siempre que cambie el código postal
  if (marker.value) {
    map.removeLayer(marker.value);
    marker.value = null;
  }
  searchResults.value = [];

  // Si hay un nuevo código postal válido, reubicar
  if (newPostalCode) {
    locateByPostalCode();
  } else {
    // Si no hay código postal, reiniciar vista general
    map.setView([19.35, -99.75], 10);
  }
});
</script>


<template>
  <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-md border dark:border-none">
    <h2 class="text-md font-semibold text-gray-900 dark:text-white mb-2">Selecciona tu ubicación</h2>
    <!-- Barra de búsqueda -->
    <div class="relative w-full">
      <input
      v-model="searchQuery"
      @input="searchAddress"
      placeholder="Buscar dirección"
      class="w-full p-2 border rounded-md mb-2"
      />

      <!-- Lista de resultados -->
      <ul
      v-if="searchResults.length"
      class="absolute top-full left-0 bg-white border rounded-md shadow-md z-50 w-full max-h-60 overflow-auto"
      >
      <li
      v-for="result in searchResults"
      :key="result.place_id"
      @click="selectAddress(result)"
      class="p-2 hover:bg-gray-200 cursor-pointer"
      >
      {{ result.display_name }}
      </li>
      </ul>
    </div>

     <!-- Contenedor del Mapa -->
      <div id="map" class="w-full h-[500px] rounded-lg shadow-md border dark:border-none z-0"></div> 
    
    <div class="text-sm text-gray-700 dark:text-white mt-2">
      <p><strong>Latitud:</strong> {{ latitude }}</p>
      <p><strong>Longitud:</strong> {{ longitude }}</p>
    </div>
    <!--- <button class="mt-3 px-10 py-5 bg-gray-800 text-white rounded-md" @click="emit('close')">Cerrar</button> --->
  </div>
</template>

<style scoped>
#map {
  width: 100%;
  height: 500px;
  border-radius: 8px;
  margin-top: 10px;
 
}
</style> 