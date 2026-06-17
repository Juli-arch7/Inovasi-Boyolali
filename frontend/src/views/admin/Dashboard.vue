<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <!-- Header -->
      <div class="page-header mb-4">
        <div class="header-top">
          <div>
            <h1 class="page-title">Dashboard Overview</h1>
            <p class="text-muted">Pantau data dan statistik inovasi terbaru di Kabupaten Boyolali.</p>
          </div>
          <div class="year-filter">
            <select class="form-control" v-model="selectedYear" @change="onYearChange">
              <option value="">Semua Tahun</option>
              <option v-for="year in availableYears" :key="year" :value="year">Tahun {{ year }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-3 mb-4">
        <div
          class="stat-card card"
          :class="{ active: activeFilter === 'all' }"
          @click="setFilter('all')"
        >
          <div class="stat-icon icon-indigo">
            <i class='bx bx-bulb'></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Total Inovasi</span>
            <h2 class="stat-value" v-if="!loadingStats">{{ stats.total_inovasi }}</h2>
            <div class="skeleton-value" v-else></div>
          </div>
        </div>

        <div
          class="stat-card card"
          :class="{ active: activeFilter === 'unit_kerja' }"
          @click="setFilter('unit_kerja')"
        >
          <div class="stat-icon icon-violet">
            <i class='bx bx-briefcase'></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Unit Kerja</span>
            <h2 class="stat-value" v-if="!loadingStats">{{ stats.unit_kerja }}</h2>
            <div class="skeleton-value" v-else></div>
          </div>
        </div>

        <div
          class="stat-card card"
          :class="{ active: activeFilter === 'masyarakat' }"
          @click="setFilter('masyarakat')"
        >
          <div class="stat-icon icon-emerald">
            <i class='bx bx-group'></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Masyarakat</span>
            <h2 class="stat-value" v-if="!loadingStats">{{ stats.masyarakat }}</h2>
            <div class="skeleton-value" v-else></div>
          </div>
        </div>
      </div>

      <!-- Chart Section -->
      <div class="card chart-section mb-4">
        <div class="chart-header">
          <h3 class="section-title">
            <i class='bx bx-bar-chart-alt-2'></i>
            Statistik Inovasi {{ selectedYear ? 'Tahun ' + selectedYear : 'Per Tahun' }}
          </h3>
        </div>
        <div class="chart-container" v-if="!loadingChart">
          <canvas ref="chartCanvas"></canvas>
        </div>
        <div class="chart-loading" v-else>
          <div class="spinner"></div>
          <span>Memuat grafik...</span>
        </div>
      </div>

      <!-- Data Table Section -->
      <div class="card table-section">
        <div class="table-header">
          <h3 class="section-title">
            <i class='bx bx-table'></i>
            Data Inovasi
            <span class="badge badge-info" v-if="tableData.total">{{ tableData.total }} data</span>
          </h3>
          <div class="table-controls">
            <div class="search-box">
              <i class='bx bx-search'></i>
              <input
                type="text"
                v-model="searchQuery"
                @input="debounceSearch"
                placeholder="Cari inovasi..."
                class="search-input"
              />
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper" v-if="!loadingTable">
          <table v-if="tableData.data && tableData.data.length > 0">
            <thead>
              <tr>
                <th class="sortable" @click="toggleSort('nama_inovasi')">
                  Nama Inovasi
                  <i :class="getSortIcon('nama_inovasi')"></i>
                </th>
                <th>Inisiator</th>
                <th>Jenis Inovasi</th>
                <th class="sortable" @click="toggleSort('tahun_inovasi')">
                  Tahun
                  <i :class="getSortIcon('tahun_inovasi')"></i>
                </th>
                <th class="sortable" @click="toggleSort('status_kurasi')">
                  Status
                  <i :class="getSortIcon('status_kurasi')"></i>
                </th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in tableData.data" :key="item.id">
                <td class="td-name">{{ item.nama_inovasi }}</td>
                <td>
                  <div class="inisiator-cell">
                    <span class="inisiator-name">{{ item.nama_inisiator }}</span>
                    <span class="inisiator-type">{{ item.jenis_inisiator }}</span>
                  </div>
                </td>
                <td>
                  <span class="badge badge-light">{{ item.jenis_inovasi }}</span>
                </td>
                <td class="td-year">{{ item.tahun_inovasi }}</td>
                <td>
                  <span :class="'badge badge-' + getStatusClass(item.status_kurasi)">
                    {{ getStatusLabel(item.status_kurasi) }}
                  </span>
                </td>
                <td>
                  <router-link
                    :to="'/admin/verifikasi/' + item.id"
                    class="btn btn-sm btn-action"
                    title="Lihat Detail"
                  >
                    <i class='bx bx-show'></i>
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="empty-state" v-else>
            <i class='bx bx-inbox'></i>
            <p>Tidak ada data inovasi ditemukan.</p>
          </div>
        </div>
        <div class="table-loading" v-else>
          <div class="spinner"></div>
          <span>Memuat data...</span>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="tableData.last_page > 1">
          <button
            class="btn btn-sm btn-outline"
            :disabled="tableData.current_page <= 1"
            @click="goToPage(tableData.current_page - 1)"
          >
            <i class='bx bx-chevron-left'></i>
          </button>
          <template v-for="page in visiblePages" :key="page">
            <button
              v-if="page !== '...'"
              class="btn btn-sm"
              :class="page === tableData.current_page ? 'btn-primary' : 'btn-outline'"
              @click="goToPage(page)"
            >
              {{ page }}
            </button>
            <span v-else class="pagination-dots">...</span>
          </template>
          <button
            class="btn btn-sm btn-outline"
            :disabled="tableData.current_page >= tableData.last_page"
            @click="goToPage(tableData.current_page + 1)"
          >
            <i class='bx bx-chevron-right'></i>
          </button>
        </div>
      </div>

      <!-- Error Toast -->
      <transition name="fade">
        <div class="error-toast" v-if="errorMsg">
          <i class='bx bx-error-circle'></i>
          {{ errorMsg }}
          <button @click="errorMsg = ''" class="toast-close"><i class='bx bx-x'></i></button>
        </div>
      </transition>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, nextTick } from 'vue'
import Sidebar from '../../components/Sidebar.vue'
import api from '../../services/api'
import Chart from 'chart.js/auto'

// ─── State ───
const stats = ref({ total_inovasi: 0, unit_kerja: 0, masyarakat: 0 })
const chartDataRaw = ref({ labels: [], data: [], type: 'yearly' })
const tableData = ref({ data: [], current_page: 1, last_page: 1, per_page: 10, total: 0 })

const selectedYear = ref('')
const activeFilter = ref('all')
const searchQuery = ref('')
const sortBy = ref('created_at')
const sortDir = ref('desc')
const currentPage = ref(1)

const loadingStats = ref(true)
const loadingChart = ref(true)
const loadingTable = ref(true)
const errorMsg = ref('')

const chartCanvas = ref(null)
let chartInstance = null
let searchTimeout = null

// ─── Available Years (derived from chart data) ───
const availableYears = ref([])

// ─── Computed: Visible Pagination Pages ───
const visiblePages = computed(() => {
  const total = tableData.value.last_page
  const current = tableData.value.current_page
  const pages = []

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    pages.push(1)
    if (current > 3) pages.push('...')
    const start = Math.max(2, current - 1)
    const end = Math.min(total - 1, current + 1)
    for (let i = start; i <= end; i++) pages.push(i)
    if (current < total - 2) pages.push('...')
    pages.push(total)
  }
  return pages
})

// ─── API Calls ───
async function fetchStatistics() {
  loadingStats.value = true
  try {
    const params = {}
    if (selectedYear.value) params.year = selectedYear.value
    const res = await api.get('/admin/dashboard/statistics', { params })
    stats.value = res.data
  } catch (e) {
    console.error('Failed to load statistics:', e)
    errorMsg.value = 'Gagal memuat statistik. Periksa koneksi server.'
  } finally {
    loadingStats.value = false
  }
}

async function fetchChart() {
  loadingChart.value = true
  try {
    const params = {}
    if (selectedYear.value) params.year = selectedYear.value
    const res = await api.get('/admin/dashboard/chart', { params })
    chartDataRaw.value = res.data

    // Extract available years from chart data if we're showing yearly view
    if (res.data.type === 'yearly' && !selectedYear.value) {
      availableYears.value = res.data.labels.map(Number).sort((a, b) => b - a)
    }

    await nextTick()
    renderChart()
  } catch (e) {
    console.error('Failed to load chart:', e)
    errorMsg.value = 'Gagal memuat grafik.'
  } finally {
    loadingChart.value = false
  }
}

async function fetchTable() {
  loadingTable.value = true
  try {
    const params = {
      filter: activeFilter.value,
      page: currentPage.value,
      per_page: 10,
      sort_by: sortBy.value,
      sort_dir: sortDir.value,
    }
    if (selectedYear.value) params.year = selectedYear.value
    if (searchQuery.value) params.search = searchQuery.value

    const res = await api.get('/admin/dashboard/inovasi', { params })
    tableData.value = res.data
  } catch (e) {
    console.error('Failed to load table:', e)
    errorMsg.value = 'Gagal memuat data inovasi.'
  } finally {
    loadingTable.value = false
  }
}

// ─── Chart Rendering ───
function renderChart() {
  if (!chartCanvas.value) return
  if (chartInstance) chartInstance.destroy()

  const ctx = chartCanvas.value.getContext('2d')

  // Create gradient
  const gradient = ctx.createLinearGradient(0, 0, 0, 300)
  gradient.addColorStop(0, 'rgba(99, 102, 241, 0.9)')
  gradient.addColorStop(1, 'rgba(99, 102, 241, 0.3)')

  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartDataRaw.value.labels,
      datasets: [{
        label: 'Jumlah Inovasi',
        data: chartDataRaw.value.data,
        backgroundColor: gradient,
        borderColor: '#6366f1',
        borderWidth: 1,
        borderRadius: 8,
        borderSkipped: false,
        barPercentage: 0.6,
        categoryPercentage: 0.7,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        intersect: false,
        mode: 'index',
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1e293b',
          titleColor: '#f8fafc',
          bodyColor: '#e2e8f0',
          borderColor: '#334155',
          borderWidth: 1,
          padding: 12,
          cornerRadius: 8,
          displayColors: false,
          callbacks: {
            title: function(items) {
              return chartDataRaw.value.type === 'yearly'
                ? 'Tahun ' + items[0].label
                : items[0].label + ' ' + selectedYear.value
            },
            label: function(item) {
              return item.raw + ' Inovasi'
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: '#94a3b8',
            font: { size: 12, family: 'Inter' },
            stepSize: 1,
          },
          grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false },
          border: { display: false },
        },
        x: {
          ticks: {
            color: '#64748b',
            font: { size: 12, family: 'Inter', weight: '500' },
          },
          grid: { display: false },
          border: { display: false },
        }
      },
      animation: {
        duration: 800,
        easing: 'easeOutQuart',
      }
    }
  })
}

// ─── Handlers ───
function setFilter(filter) {
  activeFilter.value = filter
  currentPage.value = 1
  fetchTable()
}

function onYearChange() {
  currentPage.value = 1
  fetchStatistics()
  fetchChart()
  fetchTable()
}

function debounceSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchTable()
  }, 400)
}

function toggleSort(column) {
  if (sortBy.value === column) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortDir.value = 'asc'
  }
  currentPage.value = 1
  fetchTable()
}

function goToPage(page) {
  if (page < 1 || page > tableData.value.last_page) return
  currentPage.value = page
  fetchTable()
}

function getSortIcon(column) {
  if (sortBy.value !== column) return 'bx bx-sort sort-icon'
  return sortDir.value === 'asc' ? 'bx bx-sort-up sort-icon active' : 'bx bx-sort-down sort-icon active'
}

function getStatusClass(status) {
  switch (status) {
    case 'approved': return 'success'
    case 'pending': return 'pending'
    case 'rejected': return 'danger'
    default: return 'pending'
  }
}

function getStatusLabel(status) {
  switch (status) {
    case 'approved': return 'Disetujui'
    case 'pending': return 'Menunggu'
    case 'rejected': return 'Ditolak'
    default: return status
  }
}

// ─── Init ───
onMounted(async () => {
  // First fetch chart to get available years
  await fetchChart()
  fetchStatistics()
  fetchTable()
})
</script>

<style scoped>
/* ─── Header ─── */
.header-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.year-filter select {
  padding: 0.5rem 1rem;
  border-radius: 10px;
  border: 1px solid var(--border-color);
  font-size: 0.875rem;
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  background: var(--bg-white);
  color: var(--text-main);
  cursor: pointer;
  min-width: 160px;
  transition: all 0.2s ease;
}

.year-filter select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* ─── Stat Cards ─── */
.stat-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  opacity: 0;
  transition: opacity 0.3s ease;
  border-radius: inherit;
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 10px -5px rgba(0, 0, 0, 0.04);
}

.stat-card.active {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15), 0 8px 25px -5px rgba(99, 102, 241, 0.15);
}

.stat-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
  flex-shrink: 0;
}

.icon-indigo { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5; }
.icon-violet { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #7c3aed; }
.icon-emerald { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }

.stat-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 800;
  margin: 0;
  color: var(--text-main);
  line-height: 1.2;
}

.stat-info {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.skeleton-value {
  width: 80px;
  height: 28px;
  background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 6px;
  margin-top: 4px;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ─── Chart Section ─── */
.chart-section {
  padding: 1.5rem;
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.05rem;
  font-weight: 700;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-main);
}

.section-title i {
  color: var(--primary);
}

.chart-container {
  height: 320px;
  position: relative;
}

.chart-loading,
.table-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 3rem;
  color: var(--text-muted);
  font-size: 0.9rem;
}

/* ─── Spinner ─── */
.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #e2e8f0;
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ─── Table Section ─── */
.table-section {
  padding: 1.5rem;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.table-controls {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.search-box {
  display: flex;
  align-items: center;
  background: var(--bg-gray);
  border-radius: 10px;
  padding: 0.5rem 0.875rem;
  gap: 0.5rem;
  border: 1px solid transparent;
  transition: all 0.2s ease;
}

.search-box:focus-within {
  border-color: var(--primary);
  background: var(--bg-white);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.search-box i {
  color: var(--text-light);
  font-size: 1.1rem;
}

.search-input {
  border: none;
  background: transparent;
  outline: none;
  font-family: 'Inter', sans-serif;
  font-size: 0.875rem;
  color: var(--text-main);
  width: 200px;
}

.search-input::placeholder {
  color: var(--text-light);
}

/* ─── Table ─── */
.table-wrapper {
  overflow-x: auto;
  border-radius: 10px;
  border: 1px solid var(--border-color);
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead th {
  background: var(--bg-gray);
  padding: 0.875rem 1rem;
  text-align: left;
  font-size: 0.775rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--text-muted);
  letter-spacing: 0.04em;
  white-space: nowrap;
  user-select: none;
}

th.sortable {
  cursor: pointer;
  transition: color 0.2s ease;
}

th.sortable:hover {
  color: var(--primary);
}

.sort-icon {
  font-size: 0.875rem;
  margin-left: 0.25rem;
  opacity: 0.4;
  vertical-align: middle;
}

.sort-icon.active {
  opacity: 1;
  color: var(--primary);
}

tbody td {
  padding: 0.875rem 1rem;
  border-top: 1px solid var(--border-color);
  font-size: 0.9rem;
  vertical-align: middle;
}

tbody tr {
  transition: background 0.15s ease;
}

tbody tr:hover {
  background: rgba(99, 102, 241, 0.03);
}

.td-name {
  font-weight: 600;
  color: var(--text-main);
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.td-year {
  font-weight: 600;
  font-variant-numeric: tabular-nums;
}

.inisiator-cell {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
}

.inisiator-name {
  font-weight: 500;
  color: var(--text-main);
  font-size: 0.875rem;
}

.inisiator-type {
  font-size: 0.75rem;
  color: var(--text-light);
}

/* ─── Badges ─── */
.badge-light {
  background: var(--bg-gray);
  color: var(--text-muted);
  padding: 0.25rem 0.625rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-info {
  background: #dbeafe;
  color: #1d4ed8;
  font-size: 0.7rem;
  margin-left: 0.5rem;
  padding: 0.2rem 0.5rem;
  border-radius: 6px;
  font-weight: 600;
  vertical-align: middle;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-pending { background: #fef3c7; color: #92400e; }
.badge-danger { background: #fee2e2; color: #991b1b; }

/* ─── Action Button ─── */
.btn-action {
  background: var(--bg-gray);
  color: var(--text-muted);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 0.35rem 0.6rem;
  font-size: 1.1rem;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
}

.btn-action:hover {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

/* ─── Pagination ─── */
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  padding: 1.25rem 0 0.5rem;
}

.pagination .btn {
  min-width: 36px;
  height: 36px;
  padding: 0;
  font-size: 0.8rem;
  border-radius: 8px;
}

.pagination .btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.pagination-dots {
  padding: 0 0.35rem;
  color: var(--text-light);
  font-size: 0.85rem;
}

/* ─── Empty State ─── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: var(--text-light);
  gap: 0.75rem;
}

.empty-state i {
  font-size: 3rem;
  opacity: 0.5;
}

.empty-state p {
  font-size: 0.9rem;
  margin: 0;
}

/* ─── Error Toast ─── */
.error-toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #fee2e2;
  color: #991b1b;
  padding: 0.875rem 1.25rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  box-shadow: 0 10px 25px rgba(239, 68, 68, 0.2);
  z-index: 1000;
  border: 1px solid #fca5a5;
}

.toast-close {
  background: none;
  border: none;
  color: #991b1b;
  cursor: pointer;
  font-size: 1.2rem;
  padding: 0;
  margin-left: 0.5rem;
  display: flex;
}

/* ─── Transitions ─── */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .header-top {
    flex-direction: column;
  }

  .grid-cols-3 {
    grid-template-columns: 1fr;
  }

  .table-header {
    flex-direction: column;
    align-items: stretch;
  }

  .search-input {
    width: 100%;
  }

  .td-name {
    max-width: 150px;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .grid-cols-3 {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
