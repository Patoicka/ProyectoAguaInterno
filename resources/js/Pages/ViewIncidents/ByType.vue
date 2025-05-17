<script>
import { Head } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import GenericChart from "@/Components/Charts/GenericChart.vue";
import html2canvas from "html2canvas";
import jsPDF from "jspdf";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import { mdiBallotOutline } from "@mdi/js";

export default {
    name: "ByType",
    components: {
        LayoutMain,
        Head,
        GenericChart,
        SectionTitleLineWithButton,
        HeadLogo,
    },
    methods: {
        async downloadChartsAsPDF() {
            const element = document.getElementById("charts-pdf-content");

            const canvas = await html2canvas(element, {
                backgroundColor: null,
                scale: 2,
            });

            const imgData = canvas.toDataURL("image/png");
            const pdf = new jsPDF("p", "mm", "a4");

            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

            pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
            pdf.save("graficas.pdf");
        },
    },
    props: {
        barChartData: Object,
        title: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            mdiBallotOutline,
            chartOptions: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                    },
                    title: {
                        display: true,
                        text: "Incidencias por Tipo",
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Tipo de Incidencia",
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Cantidad",
                        },
                    },
                },
            },
        };
    },
};
</script>

<template>
    <HeadLogo :title="title" />

    <LayoutMain>
        <SectionTitleLineWithButton
            :icon="mdiBallotOutline"
            :title="title"
            main
        />
        <div class="flex justify-end mb-4">
            <button
                @click="downloadChartsAsPDF"
                class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 hover:dark:bg-blue-600 border border-blue-600 dark:border-blue-500 hover:border-blue-700 hover:dark:border-blue-600 text-white py-2 px-4 rounded"
            >
                Descargar gráficas en PDF
            </button>
        </div>

        <div id="charts-pdf-content">
            <div
                class="container mx-auto p-10 bg-white dark:bg-gray-900 rounded-lg mb-4"
            >
                <GenericChart
                    :chartType="'bar'"
                    :chartData="barChartData"
                    :chartOptions="chartOptions"
                />
            </div>

            <div class="pt-3">
                <div
                    class="container p-10 bg-white dark:bg-gray-900 rounded-lg flex justify-center"
                >
                    <div class="size-1/2">
                        <GenericChart
                            :chartType="'pie'"
                            :chartData="barChartData"
                            :chartOptions="chartOptions"
                        />
                    </div>
                </div>
            </div>
        </div>
    </LayoutMain>
</template>
