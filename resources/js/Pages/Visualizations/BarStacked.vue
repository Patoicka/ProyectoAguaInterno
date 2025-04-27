<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Plotly from "plotly.js-dist-min";
import { mdiChartBarStacked } from "@mdi/js";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";

const grafico = ref(null);

const options = ["100", "1000", "10000", "Todos"];

const selectedOption = ref("100");

const datos = ref([]);

const cargarDatos = async () => {
  try {
    const limit = selectedOption.value === "Todos" ? 100 : parseInt(selectedOption.value);

    const response = await axios.get("/api/grafico/incidencias", {
      params: { limit },
    });

    datos.value = response.data;
    renderizarGrafico();
  } catch (error) {
    console.error("Error al cargar los datos del gráfico:", error);
  }
};

const renderizarGrafico = () => {
  const tipos = [...new Set(datos.value.map((item) => item.tipo))];
  const estatuses = [
    "Enviada a revisión",
    "En proceso de atención",
    "Incidencia resuelta",
  ];

  const colores = {
    "Enviada a revisión": "#f87171",
    "En proceso de atención": "#facc15",
    "Incidencia resuelta": "#4ade80",
  };

  const series = estatuses.map((estatus) => {
    return {
      name: estatus,
      type: "bar",
      x: tipos,
      y: tipos.map((tipo) => {
        const encontrado = datos.value.find(
          (i) => i.tipo === tipo && i.estatus === estatus
        );
        return encontrado ? encontrado.total : 0;
      }),
      marker: { color: colores[estatus] },
    };
  });

  const layout = {
    barmode: "stack",
    paper_bgcolor: "#1f2937",
    plot_bgcolor: "#1f2937",
    font: {
      color: "white",
      family: "Arial, sans-serif",
      size: 15,
      weight: "bold",
    },
    hoverlabel: {
      bgcolor: (val) => colores[val],
      font: {
        color: "black",
        size: 14,
        family: "Arial, sans-serif",
        weight: "500",
      },
      align: "center",
      borderpad: 10,
      namelength: -1,
    },
    xaxis: {
      tickangle: 0,
      tickfont: {
        color: "#e5e7eb",
        size: 12,
      },
      tickmode: 'array',
      tickvals: tipos,
      ticktext: tipos,
      ticklen: 12,
      ticksuffix: ' ',
      tickpad: 10,
    },
    legend: {
      orientation: "h",
      y: -0.2,
      x: 0.5,
      xanchor: 'center',
      yanchor: 'bottom',
      font: {
        color: "#f9fafb",
      },
    },
    margin: { t: 10, l: 60, r: 30, b: 100 },
    dragmode: false,
    hovermode: 'closest',
  };


  Plotly.newPlot(grafico.value, series, layout, {
    responsive: true,
    modeBarButtonsToRemove: [
      "zoom2d", "pan2d", "select2d", "lasso2d", "zoomIn2d", "zoomOut2d",
      "autoScale2d", "resetScale2d", "hoverClosestCartesian", "hoverCompareCartesian",
      "toggleSpikelines", "sendDataToCloud", "toggleHover", "resetViews",
      "tableView", "zoom3d", "pan3d", "orbitRotation", "tableRotation",
      "resetCameraDefault3d", "resetCameraLastSave3d", "hoverClosest3d",
      "orbitRotation", "turntableRotation", "zoomInGeo", "zoomOutGeo",
      "resetGeo", "hoverClosestGeo", "resetGeo"
    ],
    displaylogo: false,
    modeBarButtonsToAdd: [
      "capture"
    ]
  });
};

onMounted(() => {
  cargarDatos();
});
</script>

<template>
  <LayoutMain fullWidth>
    <SectionTitleLineWithButton :icon="mdiChartBarStacked" title="Gráfica de incidencias por tipo y estatus" main />
    <CardBox>
      <div class="flex flex-col w-full h-full">
        <div class="flex flex-col sm:flex-row w-full sm:w-fit justify-between pb-4 sm:pb-0">
          <FormField class="order-2 sm:order-1" label="Cantidad">
            <FormControl placeholder="Elige una cantidad" :options="options" v-model="selectedOption" />
          </FormField>
        </div>
      </div>

      <!-- Aseguramos que el gráfico tiene un contenedor adecuado para overflow -->
      <div class="overflow-x-auto mt-4">
        <div ref="grafico" class="w-full min-w-[1200px] sm:w-full h-[610px]"></div>
      </div>
    </CardBox>
  </LayoutMain>
</template>
