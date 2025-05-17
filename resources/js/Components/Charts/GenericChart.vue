<script>
import {
    Chart as ChartJS,
    ArcElement,
    BarElement,
    BarController,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    PieController,
} from "chart.js";

import { defineComponent, onMounted, ref, watch } from "vue";
import { Chart } from "vue-chartjs";

ChartJS.register(
    ArcElement,
    BarElement,
    BarController,
    PieController,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend
);

export default defineComponent({
    name: "GenericChart",
    components: {
        Chart,
    },
    props: {
        chartType: String,
        chartData: Object,
        chartOptions: Object,
    },
    setup(props) {
        const chartRef = ref(null);

        watch(
            () => props.chartData,
            () => {
                if (chartRef.value) {
                    chartRef.value.update();
                }
            }
        );

        return {
            chartRef,
        };
    },
});
</script>

<template>
    <div class="w-full max-w-4xl mx-auto">
        <Chart
            :type="chartType"
            :data="chartData"
            :options="chartOptions"
            ref="chartRef"
        />
    </div>
</template>
