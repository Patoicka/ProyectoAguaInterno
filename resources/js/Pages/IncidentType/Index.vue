<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import {
  mdiBallotOutline,
  mdiInformation,
  mdiPencil,
  mdiFilePdfBox,
  mdiFileExcel,
  mdiPhone,
  mdiEmail,
  mdiClipboardAccount,
  mdiEye,
} from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import { defineProps } from "vue";
import Pagination from "@/Shared/Pagination.vue";
import { ref } from "vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import Dropdown from "@/Components/DropdownTable.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import SearchBar from "@/Components/SearchBar.vue";
import { useFilters } from "@/Hooks/useFilters";
import { dateToLocal } from "@/Hooks/useFormato";
import { useCan } from "@/Hooks/usePermissions";
import BaseIcon from "@/Components/BaseIcon.vue";
import { exportToExcel, exportToPDF } from "@/Hooks/useExport";

const props = defineProps({
  name: "Index",
  title: { type: String, required: true },
  incidentTypes: {
    type: Object,
    default: () => ({}),
    required: true,
  },
  routeName: { type: String, required: true },
  filters: { type: Object, required: true },
});

const isLoading = ref(false);
const { filters, clearFilters, applyFilters } = useFilters(
  props.filters,
  props.routeName,
  isLoading
);
const headers = ["Nombre", "Descripción", "Fecha", "Estatus"];
const values = [
  "name",
  "description",
  "status",
  (item) => dateToLocal(item.created_at, true),
];

const generateExcel = () => {
  exportToExcel("TiposIncidencia", props.incidentTypes.data, headers, values);
};

const generatePdf = () => {
  exportToPDF(
    "TiposIncidencia",
    "Tipos de incidencia",
    props.incidentTypes.data,
    headers,
    values
  );
};
</script>

<template>
  <HeadLogo :title="title" />
  <div class="vl-parent">
    <loading
      v-model:active="isLoading"
      :can-cancel="false"
      :is-full-page="true"
    />
  </div>
  <LayoutMain>
    <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="title" main />

    <NotificationBar
      v-if="$page.props.flash.success"
      color="success"
      :icon="mdiInformation"
      :outline="false"
    >
      {{ $page.props.flash.success }}
    </NotificationBar>

    <NotificationBar
      v-if="$page.props.flash.error"
      color="danger"
      :icon="mdiInformation"
      :outline="false"
    >
      {{ $page.props.flash.error }}
    </NotificationBar>

    <SearchBar
      @apply-filters="applyFilters"
      @clear-filters="clearFilters"
      v-model:search="filters.search"
      v-model:rows="filters.rows"
      :routeName="routeName"
      title="tipo"
    />

    <CardBox v-if="incidentTypes.data.length > 0">
      <BaseButtons class-addon="mr-0 last:mr-0 mb-10">
        <BaseButton
          roundedNone
          :icon="mdiFilePdfBox"
          color="contrast"
          label="PDF"
          @click="generatePdf"
        />
        <BaseButton
          roundedNone
          :icon="mdiFileExcel"
          color="contrast"
          label="EXCEL"
          @click="generateExcel"
        />
      </BaseButtons>
      <table class="text-sm">
        <thead>
          <tr>
            <th />
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th class="relative inline-block text-left">
              <span
                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-md font-bold text-gray-900 dark:text-white ring-inset ring-gray-300 hover:bg-gray-100"
              >
                Acciones
              </span>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in incidentTypes.data" :key="item.id">
            <td class="align-items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                class="bi bi-book-half"
                viewBox="0 0 16 16"
              >
                <path
                  d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"
                />
              </svg>
            </td>

            <td data-label="Nombre">
              {{ item.name }}
            </td>

            <td data-label="Descripción">
              {{ item.description }}
            </td>
            <td data-label="Fecha">
              {{ dateToLocal(item.created_at, true) }}
            </td>
            <td data-label="Estatus">
              <div
                v-if="item.status"
                class="dark:bg-green-800 dark:text-green-200 bg-green-100 rounded-sm text-green-500 px-4 dark:opacity-95 opacity-85 w-min"
              >
                Activo
              </div>
              <div
                v-else
                class="dark:text-red-500 bg-red-100 rounded-sm text-red-500 px-4 dark:opacity-55 opacity-85 w-min"
              >
                Inactivo
              </div>
            </td>
            <td data-label="Acciones">
              <BaseButtons>
                <BaseButton
                  v-if="useCan('incidentType.update')"
                  color="info"
                  :icon="mdiPencil"
                  small
                  :routeName="`${routeName}edit`"
                  :parameter="item.id"
                  title="Editar tipo"
                />
              </BaseButtons>
            </td>
          </tr>
        </tbody>
      </table>
    </CardBox>
    <CardBoxComponentEmpty v-else />
    <pagination
      :links="incidentTypes.links"
      :total="incidentTypes.total"
      :to="incidentTypes.to"
      :from="incidentTypes.from"
    />
  </LayoutMain>
</template>
