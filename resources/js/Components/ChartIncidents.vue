<script setup>
import { ref, onMounted, watch } from "vue";
import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

Chart.register(...registerables, ChartDataLabels);

const props = defineProps({
    data: Array,
    labels: Array,
});

const generateColor = (key) => {
    let hash = 0;
    for (let i = 0; i < key.length; i++) {
        hash = key.charCodeAt(i) + ((hash << 5) - hash);
    }
    return `hsl(${Math.abs(hash) % 360}, 80%, 50%)`;
};

const barChartCanvas = ref(null);
const pieChartCanvas = ref(null);
let barChartInstance = null;
let pieChartInstance = null;

const renderCharts = () => {
    if (barChartInstance) barChartInstance.destroy();
    if (pieChartInstance) pieChartInstance.destroy();

    barChartInstance = new Chart(barChartCanvas.value, {
        type: "bar",
        data: {
            labels: props.labels,
            datasets: [
                {
                    label: "Incidencias",
                    data: props.data,
                    backgroundColor: props.labels.map((label) =>
                        generateColor(label)
                    ),
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: "center",
                    align: "center",
                    formatter: (value, context) => {
                        const total = context.dataset.data.reduce(
                            (acc, val) => acc + val,
                            0
                        );
                        const percentage = ((value / total) * 100).toFixed(2);
                        return `${percentage}%`;
                    },
                    font: {
                        size: 12,
                        weight: "bold",
                    },
                    color: "#fff",
                },
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: "Municipios",
                    },
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Número  Incidencias",
                    },
                },
            },
        },
    });

    pieChartInstance = new Chart(pieChartCanvas.value, {
        type: "pie",
        data: {
            labels: props.labels,
            datasets: [
                {
                    label: "Distribución",
                    data: props.data,
                    backgroundColor: props.labels.map((label) =>
                        generateColor(label)
                    ),
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        font: {
                            size: 12,
                        },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            let total = tooltipItem.dataset.data.reduce(
                                (acc, value) => acc + value,
                                0
                            );
                            let percentage = (
                                (tooltipItem.raw / total) *
                                100
                            ).toFixed(2);
                            return `${tooltipItem.label}: ${tooltipItem.raw} (${percentage}%)`;
                        },
                    },
                },
            },
        },
    });
};

onMounted(renderCharts);
watch(() => props.data, renderCharts);
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-3 bg-white dark:bg-gray-900 rounded-lg shadow-md">
            <h3
                class="text-sm font-semibold text-gray-900 dark:text-white mb-2"
            >
                Incidencias por Municipio
            </h3>
            <div class="h-[300px]"><canvas ref="barChartCanvas"></canvas></div>
        </div>
        <div class="p-3 bg-white dark:bg-gray-900 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                Distribución
            </h3>
            <div class="h-[300px]"><canvas ref="pieChartCanvas"></canvas></div>
        </div>
    </div>
</template>

