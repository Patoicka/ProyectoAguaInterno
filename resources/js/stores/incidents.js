import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useIncidentStore = defineStore("incident", () => {
    const incidentTypeId = ref(null);
    const incidentTypes = ref([]);  // Aquí guardarás los tipos de incidencia de la API

    const selectedIncidentName = computed(() => {
        return incidentTypes.value.find(i => i.id === incidentTypeId.value)?.name || "";
    });

    return { incidentTypeId, incidentTypes, selectedIncidentName };
});
