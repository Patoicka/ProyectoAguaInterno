<script>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { Link, Head, router } from "@inertiajs/vue3";
import { computed, onMounted, reactive, ref, watch } from "vue";
import axios from "axios";
import Chart from "chart.js/auto";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import ChartDataLabels from "chartjs-plugin-datalabels"; // Importar el plugin de etiquetas

export default {
  name: "IncidentChart",

  setup() {
    const incidentData = ref([]); // Datos del API
    const chart = ref(null); // Referencia al gráfico
    const incidentNames = ref([]); // Nombres de los incidentes

    // Cargar datos del API
    const loadFilteredData = () => {
      axios
        .get(route("welcome.incidentRegister"))
        .then((response) => {
          incidentData.value = response.data;
          incidentNames.value = response.data[response.data.length - 1]; // Último elemento con nombres
          updateChart();
        })
        .catch((error) => {
          console.error("Error al cargar datos:", error);
        });
    };

    // Función para actualizar el gráfico
    const updateChart = () => {
      if (!incidentData.value.length) return;

      // Extraer etiquetas (años) y datos dinámicamente
      const labelsData = incidentData.value[0]; // Años
      const datasets = [];

      // Recorrer todos los tipos de incidentes (sin incluir el total)
      for (let i = 2; i < incidentData.value.length - 1; i++) {
        datasets.push({
          label: incidentNames.value[i-2], // Asignar el nombre correcto
          data: incidentData.value[i],
          backgroundColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.5)`,
          borderColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`,
          borderWidth: 2,
        });
      }

      // Eliminar gráfico previo si existe
      const existingChart = Chart.getChart("incidentChart");
      if (existingChart) {
        existingChart.destroy();
      }

      // Crear nuevo gráfico
      const ctx = document.getElementById("incidentChart").getContext("2d");
      chart.value = new Chart(ctx, {
        type: "bar", // Solo barras
        data: {
          labels: labelsData,
          datasets: datasets,
        },
        options: {
          responsive: true,
          plugins: {
            legend: { 
              display: true, 
              position: "top", // Cambia a "bottom" si la quieres en la parte inferior
              labels: {
                boxWidth: 15, // Hace que los cuadrados de la leyenda sean más pequeños
                padding: 15, // Añade espacio para que no se sobreponga
                font: {
                  size: 12
                }
              }
            },
            datalabels: { // Configuración de etiquetas de datos
              anchor: "end",
              align: "top",
              formatter: (value) => value, // Muestra el valor numérico
              font: {
                weight: "bold",
                size: 12
              },
              color: "#000"
            },
          },
          scales: {
            x: {
              title: {
                display: true,
                text: "Años",
                font: { weight: "bold" },
              },
            },
            y: {
              title: {
                display: true,
                text: "Número de Incidencias Registradas",
                font: { weight: "bold" },
              },
              beginAtZero: true,
            },
          },
        },
        plugins: [ChartDataLabels], // Activar etiquetas
      });
    };

    onMounted(() => {
      loadFilteredData();
    });

    return {
      incidentData,
      chart,
      loadFilteredData,
      updateChart,
    };
  },
};
</script>

<template>
  <div class="w-full">
    <canvas id="incidentChart"></canvas>
  </div>
</template>