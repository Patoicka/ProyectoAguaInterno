<!-- resources/js/Pages/Visualizations/IncidentGraph.vue -->
<script setup>
/* ------------------------------------------------------------------ */
/*  IMPORTS                                                           */
/* ------------------------------------------------------------------ */
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

import HeadLogo from '@/Components/HeadLogo.vue'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue'
import CardBox from '@/Components/CardBox.vue'
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Button from '@/Components/Button.vue'
import {
  mdiMapMarkerAlertOutline,
  mdiFilePdfBox,
  mdiFileExcelBox
} from '@mdi/js'

/* ╔══════════════════════════════════════════════════════════════╗ */
/* ║   LISTA BLANCA — tablas y columnas permitidos (solo front)    ║ */
/* ╚══════════════════════════════════════════════════════════════╝ */
const allowedColumns = {
  incidents: ['description', 'unique_code', 'created_at', 'updated_at'],
  cities: ['id', 'name', 'code'],
  states: ['name', 'code'],
  reports: ['names', 'firts_surname', 'second_surname', 'created_at'],
  neighborhoods: ['name', 'code', 'postal_code'],
  locations: [
    'street', 'inter_number', 'exterior_number', 'additional',
    'references', 'neighborhood_id', 'lat', 'lng'
  ],
  incident_types: ['name'],
  incident_status: ['name'],
  contacts: ['number', 'contact_email', 'created_at']
}

/* ------------------------------------------------------------------ */
/*  PARTE 1 — GRÁFICA DE INCIDENCIAS                                  */
/* ------------------------------------------------------------------ */
const chartInstance = ref(null)
const availableFilters = ref({ years: [], types: [], statuses: [], cities: [] })
const filtros = ref({ years: [], types: [], statuses: [], cities: [] })

const destroyChart = () => {
  if (chartInstance.value) {
    chartInstance.value.stop()
    chartInstance.value.destroy()
    chartInstance.value = null
  }
}

const buildChart = payload => ({
  type: 'bar',
  data: { labels: payload.labels, datasets: payload.datasets },
  options: {
    responsive: true,
    plugins: {
      title: { display: true, text: 'Gráfica de Incidencias' },
      legend: { position: 'bottom' }
    },
    scales: {
      x: { title: { display: true, text: 'Año' } },
      y: { title: { display: true, text: 'Número de Incidencias' }, beginAtZero: true }
    }
  }
})

const fetchFilters = async () => {
  const { data } = await axios.get(route('incident.available-filters'))
  availableFilters.value = data
}

const fetchChart = async (alerta = false) => {
  const params = {}
  if (filtros.value.years.length) params.years = filtros.value.years
  if (filtros.value.types.length) params.types = filtros.value.types
  if (filtros.value.statuses.length) params.statuses = filtros.value.statuses
  if (filtros.value.cities.length) params.cities = filtros.value.cities

  const { data } = await axios.get(route('incident.chart'), { params })
  const ctx = document.getElementById('chart')?.getContext('2d')
  if (!ctx) return

  const vacia = !data?.labels?.length || !data?.datasets?.length
  if (vacia) {
    destroyChart()
    if (alerta) window.alert('Sin datos para esos filtros.')
    return
  }

  destroyChart()
  chartInstance.value = new Chart(ctx, buildChart(data))
}

const clearFilters = () => {
  filtros.value = { years: [], types: [], statuses: [], cities: [] }
  fetchChart()
}

const exportPdf = () => {
  if (!filtros.value.years.length) return window.alert('Selecciona al menos un año')
  const p = new URLSearchParams({ anio: filtros.value.years[0] })
  filtros.value.types.forEach(t => p.append('tipos[]', t))
  filtros.value.statuses.forEach(s => p.append('status[]', s))
  filtros.value.cities.forEach(c => p.append('city[]', c))
  window.open(`${route('incident.exportPdf')}?${p}`, '_blank')
}

/* ------------------------------------------------------------------ */
/*  PARTE 2 — REPORTE DINÁMICO (PDF / CSV)                            */
/* ------------------------------------------------------------------ */
const tablas = ref(Object.keys(allowedColumns))
const tablaSeleccion = ref('incidents')
const columnas = ref([])
const columnasCheck = ref([])
const filtrosExtra = ref([{ col: '', val: '' }])
const anioReporte = ref('')
const fechaInicio = ref('')
const fechaFin = ref('')
const limite = ref(1000)
const limiteSearch = ref(1000)

watch(tablaSeleccion, async t => {
  if (!t) return
  const { data } = await axios.get('/api/table-columns', { params: { table: t } })
  columnas.value = data.filter(c => allowedColumns[t]?.includes(c))
  columnasCheck.value = []
}, { immediate: true })

const addFiltro = () => filtrosExtra.value.push({ col: '', val: '' })
const removeFiltro = idx => filtrosExtra.value.splice(idx, 1)

const buildDynamicUrl = () => {
  const p = new URLSearchParams({ table: tablaSeleccion.value })
  if (anioReporte.value) p.append('anio', anioReporte.value)
  if (fechaInicio.value) p.append('fecha_inicio', fechaInicio.value)
  if (fechaFin.value) p.append('fecha_fin', fechaFin.value)
  if (columnasCheck.value.length) p.append('cols', columnasCheck.value.join(','))
  if (limite.value) p.append('limit', limite.value)

  filtrosExtra.value.forEach(f => {
    if (f.col && f.val && allowedColumns[tablaSeleccion.value]?.includes(f.col))
      p.append(f.col, f.val)
  })
  return `/api/dynamic-report?${p.toString()}`
}

const descargarDynamic = fmt =>
  window.open(buildDynamicUrl() + `&format=${fmt}`, '_blank')

const verDynamicJson = async () => {
  const { data } = await axios.get(buildDynamicUrl(), { headers: { Accept: 'application/json' } })
  console.table(data)
}

/* ------------------------------------------------------------------ */
/*  PARTE 3 — CONSULTA DINÁMICA (preview en pantalla)                 */
/* ------------------------------------------------------------------ */
const searchTable = ref('cities')
const q = ref('')
const perPage = ref(10)
const page = ref(1)
const fechaInicioS = ref('')
const fechaFinS = ref('')
const rows = ref([])
const colsSearch = ref([])
const total = ref(0)
const loading = ref(false)

const availableSearchFilters = ref({})
const selectedSearchFilters = ref({})

const fetchSearchFilters = async () => {
  const { data } = await axios.get('/api/dynamic-filters', { params: { table: searchTable.value } })
  const filtered = Object.fromEntries(
    Object.entries(data).filter(([c]) => allowedColumns[searchTable.value]?.includes(c))
  )
  availableSearchFilters.value = filtered
  selectedSearchFilters.value = Object.fromEntries(Object.keys(filtered).map(k => [k, []]))
}

const fetchSearch = async () => {
  loading.value = true
  try {
    const params = {
      table: searchTable.value,
      q: q.value,
      cols: allowedColumns[searchTable.value]?.join(','),
      page: page.value,
      per_page: perPage.value,
      fecha_inicio: fechaInicioS.value,
      fecha_fin: fechaFinS.value,
      ...Object.fromEntries(
        Object.entries(selectedSearchFilters.value).filter(([, arr]) => arr.length)
      )
    }
    const { data } = await axios.get('/api/dynamic-search', { params })
    rows.value = data.data
    total.value = data.total
    colsSearch.value = rows.value.length ? Object.keys(rows.value[0]) : []
  } finally { loading.value = false }
}

/* exportar búsqueda activa (sin paginación) */
const buildSearchParams = () => {
  const p = new URLSearchParams({
    table: searchTable.value,
    cols: allowedColumns[searchTable.value]?.join(','),
    q: q.value,
    fecha_inicio: fechaInicioS.value,
    fecha_fin: fechaFinS.value
  })
  if (limiteSearch.value) p.append('limit', limiteSearch.value)

  Object.entries(selectedSearchFilters.value).forEach(([col, arr]) =>
    arr.forEach(v => p.append(`${col}[]`, v))
  )
  return p.toString()
}

const exportSearch = fmt =>
  window.open(`/api/dynamic-report?${buildSearchParams()}&format=${fmt}`, '_blank')

watch(searchTable, async () => { page.value = 1; await fetchSearchFilters(); await fetchSearch() })
watch(perPage, () => { page.value = 1; fetchSearch() })

const nextPage = () => { if (page.value * perPage.value < total.value) { page.value++; fetchSearch() } }
const prevPage = () => { if (page.value > 1) { page.value--; fetchSearch() } }

/* ------------------------------------------------------------------ */
/*  MONTAJE                                                           */
/* ------------------------------------------------------------------ */
onMounted(async () => {
  await fetchFilters()
  await fetchChart()
  await fetchSearchFilters()
  await fetchSearch()
})
</script>
<template>
  <HeadLogo title="Conagua" />

  <LayoutMain>
    <SectionTitleLineWithButton :icon="mdiMapMarkerAlertOutline" title="Dashboard - Incidencias" main />


    <!-- ╔═════════════════════ GRÁFICA ═════════════════════╗ -->
    <CardBox>
      <!-- filtros -->
      <div class="flex flex-wrap gap-4 pb-4">
        <FormField label="Años">
          <FormControl :options="availableFilters.years" v-model="filtros.years" @change="fetchChart(true)" />
        </FormField>
        <FormField label="Tipo">
          <FormControl :options="availableFilters.types" v-model="filtros.types" @change="fetchChart(true)" />
        </FormField>
        <FormField label="Estado">
          <FormControl :options="availableFilters.statuses" v-model="filtros.statuses" @change="fetchChart(true)" />
        </FormField>
        <FormField label="Municipio">
          <FormControl :options="availableFilters.cities" v-model="filtros.cities" @change="fetchChart(true)" />
        </FormField>
        <FormField label="Acciones">
          <PrimaryButton @click="clearFilters">Limpiar</PrimaryButton>
          <Button class="ml-2" color="redWhite" :icon="mdiFilePdfBox" @click="exportPdf">Exportar PDF</Button>
        </FormField>
      </div>
      <canvas id="chart" height="45"></canvas>
    </CardBox>

    <!-- ╔════════════════════ REPORTE (PDF/CSV) ════════════════════╗ -->
    <CardBox class="mt-6">
      <h3 class="text-lg font-semibold mb-2">Reporte dinámico (PDF / CSV)</h3>

      <div class="flex flex-wrap gap-4 mb-4">
        <FormField label="Tabla">
          <FormControl :options="tablas" v-model="tablaSeleccion" />
        </FormField>
        <FormField label="Año (opcional)">
          <input v-model="anioReporte" type="number" class="p-2 rounded bg-gray-700 text-white w-32" />
        </FormField>
        <FormField label="Fecha inicio">
          <input v-model="fechaInicio" type="date" class="p-2 rounded bg-gray-700 text-white" />
        </FormField>
        <FormField label="Fecha fin">
          <input v-model="fechaFin" type="date" class="p-2 rounded bg-gray-700 text-white" />
        </FormField>
        <FormField label="Máx filas (limit)">
          <input v-model.number="limite" type="number" min="1" class="p-2 rounded bg-gray-700 text-white w-28" />
        </FormField>
      </div>

      <!-- columnas -->
      <div class="mb-4">
        <p class="text-sm mb-1 text-gray-300">Columnas:</p>
        <div class="flex flex-wrap gap-3">
          <label v-for="c in columnas" :key="c" class="text-sm text-gray-200">
            <input type="checkbox" :value="c" v-model="columnasCheck" class="mr-1" /> {{ c }}
          </label>
        </div>
      </div>

      <!-- filtros libres -->
      <div class="mb-4">
        <p class="text-sm mb-1 text-gray-300">Filtros (columna = valor):</p>
        <div v-for="(f, idx) in filtrosExtra" :key="idx" class="flex gap-2 mb-1">
          <input v-model="f.col" placeholder="columna" class="p-1 rounded w-40 bg-gray-700 text-white" />
          <input v-model="f.val" placeholder="valor" class="p-1 rounded w-40 bg-gray-700 text-white" />
          <button @click="removeFiltro(idx)" class="text-red-400">x</button>
        </div>
        <button @click="addFiltro" class="text-blue-300 text-sm">+ filtro</button>
      </div>

      <!-- acciones -->
      <div class="flex gap-3">
        <Button color="blueWhite" @click="verDynamicJson">Ver JSON</Button>

        <Button :icon="mdiFilePdfBox" color="redWhite" @click="descargarDynamic('pdf')">
          PDF
        </Button>
        <Button :icon="mdiFileExcelBox" color="greenWhite" @click="descargarDynamic('csv')">
          Excel
        </Button>
      </div>
    </CardBox>

    <!-- ╔════════════════════ CONSULTA DINÁMICA ════════════════════╗ -->
    <CardBox class="mt-6">
      <h3 class="text-lg font-semibold mb-2">Consulta dinámica</h3>

      <div class="flex flex-wrap gap-4 mb-4">
        <FormField label="Tabla">
          <FormControl :options="tablas" v-model="searchTable" />
        </FormField>
        <FormField label="Buscar">
          <input v-model="q" @keyup.enter="page = 1; fetchSearch()" placeholder="texto libre"
            class="p-2 rounded bg-gray-700 text-white w-52" />
        </FormField>
        <FormField label="Fecha inicio">
          <input v-model="fechaInicioS" type="date" class="p-2 rounded bg-gray-700 text-white" />
        </FormField>
        <FormField label="Fecha fin">
          <input v-model="fechaFinS" type="date" class="p-2 rounded bg-gray-700 text-white" />
        </FormField>
        <FormField label="Por página">
          <input v-model.number="perPage" type="number" min="1" class="p-2 rounded bg-gray-700 text-white w-24" />
        </FormField>
        <FormField label="Acciones">
          <PrimaryButton @click="page = 1; fetchSearch()">Buscar</PrimaryButton>
        </FormField>
      </div>

      <!-- filtros automáticos -->
      <div v-if="Object.keys(availableSearchFilters).length" class="flex flex-wrap gap-4 mb-4">
        <FormField v-for="(opts, col) in availableSearchFilters" :key="col" :label="col">
          <FormControl multiple :options="opts" v-model="selectedSearchFilters[col]"
            @change="page = 1; fetchSearch()" />
        </FormField>
      </div>

      <!-- resultados -->
      <div v-if="loading" class="text-center py-6">Cargando…</div>

      <div v-else>
        <table v-if="rows.length" class="min-w-full text-sm text-gray-200 border border-gray-600">
          <thead class="bg-gray-800">
            <tr>
              <th v-for="c in colsSearch" :key="c" class="px-3 py-2 border">{{ c }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(r, idx) in rows" :key="idx" :class="idx % 2 ? 'bg-gray-800/40' : ''">
              <td v-for="c in colsSearch" :key="c" class="px-3 py-1 border">{{ r[c] }}</td>
            </tr>
          </tbody>
        </table>

        <div v-else class="text-center py-4">Sin resultados.</div>

        <!-- paginación -->
        <div v-if="total > perPage" class="flex items-center gap-3 mt-4">
          <Button :disabled="page === 1" @click="prevPage">Anterior</Button>
          <span>Página {{ page }}</span>
          <Button :disabled="page * perPage >= total" @click="nextPage">Siguiente</Button>
          <span class="ml-auto text-sm text-gray-400">{{ total }} resultados</span>
        </div>
        <FormField label="Máx filas (PDF/CSV)">
          <input v-model.number="limiteSearch" type="number" min="1" class="p-2 rounded bg-gray-700 text-white w-28" />
        </FormField>
        <!-- ► EXPORTAR RESULTADOS FILTRADOS (PDF | Excel) ◄ -->
        <div v-if="rows.length" class="flex gap-3 mt-4">
          <Button :icon="mdiFilePdfBox" color="redWhite" @click="exportSearch('pdf')">
            PDF
          </Button>
          <Button :icon="mdiFileExcelBox" color="greenWhite" @click="exportSearch('csv')">
            Excel
          </Button>
        </div>
      </div>
    </CardBox>
  </LayoutMain>
</template>
