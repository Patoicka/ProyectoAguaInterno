<script>
import { Head } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import GenericChart from "@/Components/Charts/GenericChart.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiBallotOutline } from "@mdi/js";
import html2canvas from "html2canvas";
import jsPDF from "jspdf";

export default {
    name: "ByDate",
    components: {
        LayoutMain,
        Head,
        GenericChart,
        SectionTitleLineWithButton,
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
        lineChartData: Object,
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
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Fecha",
                        },
                    },
                    y: {
                        title: {
                            display: true,
                            text: "Cantidad de Incidencias",
                        },
                        beginAtZero: true,
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
                class="container p-10 bg-white dark:bg-gray-900 rounded-lg flex justify-center"
            >
                <GenericChart
                    :chartType="'line'"
                    :chartData="lineChartData"
                    :chartOptions="chartOptions"
                />
            </div>
        </div>
    </LayoutMain>
</template>
