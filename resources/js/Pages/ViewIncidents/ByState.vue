<script>
import { Head } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import GenericChart from "@/Components/Charts/GenericChart.vue";

export default {
  name: "ByState",
  components: {
    LayoutMain,
    Head,
    GenericChart,
  },
  data() {
    return {
      chartOptions: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top',
          },
          title: {
            display: true,
            text: 'Incidencias por Estado',
          },
        },
      },
    };
  },
  props: {
    barChartData: Object,
  }
};
</script>

<template>
  <LayoutMain>
    <Head title="Incidencias Por Estado" />
    <div class="container mx-auto">
      <h1 class="text-lg font-bold mb-4">Incidencias Por Estado</h1>
      <GenericChart
        :chartType="'bar'"
        :chartData="barChartData"
        :chartOptions="chartOptions"
      />
      
    </div>
  </LayoutMain>
</template>
