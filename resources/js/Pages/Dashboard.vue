<script setup>
import { Head } from "@inertiajs/vue3";
import { computed, ref, onMounted } from "vue";
import { useMainStore } from "@/stores/main";
import {
  mdiAccountMultiple,
  mdiBookEducation,
  mdiChartTimelineVariant,
  mdiMonitorCellphone,
  mdiReload,
  mdiGithub,
  mdiChartPie,
  mdiViewModule,
  mdiBullhorn,
} from "@mdi/js";
import * as chartConfig from "@/Components/Charts/chart.config.js";
import LineChart from "@/Components/Charts/LineChart.vue";
import SectionMain from "@/Components/SectionMain.vue";
import CardBoxWidget from "@/Components/CardBoxWidget.vue";
import CardBox from "@/Components/CardBox.vue";
import TableSampleClients from "@/Components/TableSampleClients.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBoxTransaction from "@/Components/CardBoxTransaction.vue";
import CardBoxClient from "@/Components/CardBoxClient.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import SectionBannerStarOnGitHub from "@/Components/SectionBannerStarOnGitHub.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import GraphDashboard from "@/Components/GraphDashboard.vue";

const props = defineProps({
  data: {
    type: Object,
    default: {},
  },
  users: {
    type: Number,
    default: null,
  },
  IndicentType:{
    type: Number,
    default:null
  },
  Incident:{
    type: Number,
    default:null
  }
});

const chartData = ref(null);

const fillChartData = () => {
  chartData.value = chartConfig.sampleChartData();
};

onMounted(() => {
  fillChartData();
});
</script>

<template>
  <HeadLogo title="Inicio" />
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Descripción general" main>
      </SectionTitleLineWithButton>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
        <CardBoxWidget trend="Incidencias" trend-type="up" color="text-emerald-500" :icon="mdiAlertCircle"
          :number="Incident" label="Incidencias Registradas" direccion="incident"/>

        <CardBoxWidget trend="Usuarios" trend-type="up" color="text-emerald-500" :icon="mdiAccountMultiple"
          :number="users" label="Usuarios Registrados" direccion="user" />
        
          <CardBoxWidget trend="Tipos de Incidencias" trend-type="up" color="text-emerald-500" :icon="mdiAccountMultiple"
          :number="IndicentType" label="Tipos de Incidencias" direccion="incidentType"/>
      </div>

      <SectionTitleLineWithButton :icon="mdiChartPie" title="Tipos de Incidencias Registradas por Año">
        <BaseButton :icon="mdiDomain" href="/incident" />
      </SectionTitleLineWithButton>

      <CardBox class="mb-6">
        <GraphDashboard/>
      </CardBox>

    </SectionMain>
  </LayoutAuthenticated>
</template>
