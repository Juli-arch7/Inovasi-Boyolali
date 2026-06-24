<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />
    
    <main class="flex-1 p-6 sm:p-8 pt-10 sm:pt-12 overflow-visible max-w-7xl mx-auto w-full">
      <!-- Sticky Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50 sticky top-16 bg-slate-50/90 dark:bg-slate-950/90 backdrop-blur-md z-20">
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            Dashboard Overview
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-250/50 dark:border-emerald-900/30">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-ping"></span>
              REALTIME STATS
            </span>
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Pantau analitik, kurasi, dan data aktivitas portal inovasi secara langsung.</p>
        </div>
        
        <!-- Refresh Timer Indicator -->
        <div class="flex items-center gap-2 self-start sm:self-center px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 shadow-sm text-xs font-semibold text-slate-555 dark:text-slate-400">
          <RefreshCw class="w-3.5 h-3.5 animate-spin text-primary" />
          <span>Syncing every 5s</span>
        </div>
      </div>

      <!-- Statistics Widgets (8 Grid) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-10 sm:mt-12 mb-8">
        
        <!-- Total Inovasi -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/40 dark:border-slate-800/40 shadow-sm hover:shadow-md transition-all group">
          <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
          <div class="flex justify-between items-start">
            <div class="space-y-2">
              <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">Total Inovasi</span>
              <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">{{ formatCount(stats.total) }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center text-primary">
              <Lightbulb class="w-6 h-6" />
            </div>
          </div>
        </div>

        <!-- Disetujui -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/40 dark:border-slate-800/40 shadow-sm hover:shadow-md transition-all group">
          <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
          <div class="flex justify-between items-start">
            <div class="space-y-2">
              <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">Disetujui</span>
              <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">{{ formatCount(stats.approved) }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-500">
              <CheckCircle2 class="w-6 h-6" />
            </div>
          </div>
        </div>

        <!-- Pending -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/40 dark:border-slate-800/40 shadow-sm hover:shadow-md transition-all group">
          <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
          <div class="flex justify-between items-start">
            <div class="space-y-2">
              <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">Pending</span>
              <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">{{ formatCount(stats.pending) }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-500">
              <Clock class="w-6 h-6" />
            </div>
          </div>
        </div>

        <!-- Ditolak -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/40 dark:border-slate-800/40 shadow-sm hover:shadow-md transition-all group">
          <div class="absolute top-0 left-0 w-1.5 h-full bg-rose-500"></div>
          <div class="flex justify-between items-start">
            <div class="space-y-2">
              <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">Ditolak</span>
              <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">{{ formatCount(stats.rejected) }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center text-rose-500">
              <AlertTriangle class="w-6 h-6" />
            </div>
          </div>
        </div>

        <!-- Total User -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/40 dark:border-slate-800/40 shadow-sm hover:shadow-md transition-all group">
          <div class="absolute top-0 left-0 w-1.5 h-full bg-slate-700"></div>
          <div class="flex justify-between items-start">
            <div class="space-y-2">
              <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">Total Pengguna</span>
              <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">{{ formatCount(stats.total_users) }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300">
              <Users class="w-6 h-6" />
            </div>
          </div>
        </div>

        <!-- Total View -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/40 dark:border-slate-800/40 shadow-sm hover:shadow-md transition-all group">
          <div class="absolute top-0 left-0 w-1.5 h-full bg-cyan-500"></div>
          <div class="flex justify-between items-start">
            <div class="space-y-2">
              <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">Total Views</span>
              <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">{{ formatCount(stats.total_views) }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 flex items-center justify-center text-cyan-500">
              <Eye class="w-6 h-6" />
            </div>
          </div>
        </div>

        <!-- Total Likes -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/40 dark:border-slate-800/40 shadow-sm hover:shadow-md transition-all group">
          <div class="absolute top-0 left-0 w-1.5 h-full bg-rose-450"></div>
          <div class="flex justify-between items-start">
            <div class="space-y-2">
              <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">Total Likes</span>
              <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">{{ formatCount(stats.total_likes) }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center text-rose-500">
              <Heart class="w-6 h-6" />
            </div>
          </div>
        </div>

        <!-- Total Download -->
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/40 dark:border-slate-800/40 shadow-sm hover:shadow-md transition-all group">
          <div class="absolute top-0 left-0 w-1.5 h-full bg-purple-500"></div>
          <div class="flex justify-between items-start">
            <div class="space-y-2">
              <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">Downloads</span>
              <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight group-hover:scale-105 transition-transform duration-300">{{ formatCount(stats.total_downloads) }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center text-purple-500">
              <Download class="w-6 h-6" />
            </div>
          </div>
        </div>

      </div>

      <!-- Charts Section (Grid) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Line Chart: Monthly Growth -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800 p-6 shadow-sm">
          <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
            <span class="w-1 h-5 rounded bg-primary"></span>
            Perkembangan Inovasi Bulanan
          </h3>
          <div class="h-[300px]">
            <apexchart 
              type="line" 
              height="100%" 
              :options="lineChartOptions" 
              :series="lineChartSeries"
            ></apexchart>
          </div>
        </div>

        <!-- Area Chart: User Session Activity -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800 p-6 shadow-sm">
          <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
            <span class="w-1 h-5 rounded bg-secondary"></span>
            Aktivitas Kunjungan User (7 Hari Terakhir)
          </h3>
          <div class="h-[300px]">
            <apexchart 
              type="area" 
              height="100%" 
              :options="areaChartOptions" 
              :series="areaChartSeries"
            ></apexchart>
          </div>
        </div>

        <!-- Bar Chart: Category Breakdown -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800 p-6 shadow-sm">
          <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
            <span class="w-1 h-5 rounded bg-accent"></span>
            Inovasi Berdasarkan Kategori
          </h3>
          <div class="h-[300px]">
            <apexchart 
              type="bar" 
              height="100%" 
              :options="barChartOptions" 
              :series="barChartSeries"
            ></apexchart>
          </div>
        </div>

        <!-- Pie Chart: status breakdown -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800 p-6 shadow-sm">
          <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
            <span class="w-1 h-5 rounded bg-emerald-500"></span>
            Status Verifikasi Produk
          </h3>
          <div class="h-[300px]">
            <apexchart 
              type="pie" 
              height="100%" 
              :options="pieChartOptions" 
              :series="pieChartSeries"
            ></apexchart>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import api from '../../services/api'
import { useToastStore } from '../../stores/toast'
import {
  Lightbulb,
  CheckCircle2,
  Clock,
  AlertTriangle,
  Users,
  Eye,
  Heart,
  Download,
  RefreshCw
} from 'lucide-vue-next'

const toastStore = useToastStore()

const stats = ref({
  total: 0,
  approved: 0,
  pending: 0,
  rejected: 0,
  total_users: 0,
  total_views: 0,
  total_likes: 0,
  total_downloads: 0,
  per_bulan: [],
  per_bentuk: [],
  aktivitas_user: []
})

async function loadStats() {
  try {
    const res = await api.get('/admin/stats')
    stats.value = res.data
  } catch (e) {
    console.error('Failed to load real-time stats', e)
  }
}

// 1. Line Chart (Monthly Growth) Configuration
const lineChartOptions = computed(() => {
  const isDarkTheme = document.documentElement.classList.contains('dark')
  return {
    chart: {
      type: 'line',
      toolbar: { show: false },
      zoom: { enabled: false },
      background: 'transparent'
    },
    colors: ['#2563EB'], // Primary blue
    stroke: { curve: 'smooth', width: 3.5 },
    markers: { size: 4, strokeWidth: 2, hover: { size: 6 } },
    xaxis: {
      categories: stats.value.per_bulan?.map(b => b.label) || [],
      labels: { style: { colors: isDarkTheme ? '#94a3b8' : '#64748b' } }
    },
    yaxis: {
      labels: {
        style: { colors: isDarkTheme ? '#94a3b8' : '#64748b' },
        formatter: (val) => val.toFixed(0)
      }
    },
    grid: { borderColor: isDarkTheme ? '#334155' : '#f1f5f9' },
    theme: { mode: isDarkTheme ? 'dark' : 'light' },
    tooltip: { theme: isDarkTheme ? 'dark' : 'light' }
  }
})

const lineChartSeries = computed(() => [{
  name: 'Jumlah Inovasi',
  data: stats.value.per_bulan?.map(b => b.total) || []
}])

// 2. Area Chart (User Session Activity) Configuration
const areaChartOptions = computed(() => {
  const isDarkTheme = document.documentElement.classList.contains('dark')
  return {
    chart: {
      type: 'area',
      toolbar: { show: false },
      background: 'transparent'
    },
    colors: ['#06B6D4', '#2563EB'], // Cyan, Blue
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.35,
        opacityTo: 0.05,
        stops: [0, 90, 100]
      }
    },
    stroke: { curve: 'smooth', width: 2.5 },
    xaxis: {
      categories: stats.value.aktivitas_user?.map(u => u.tanggal) || [],
      labels: { style: { colors: isDarkTheme ? '#94a3b8' : '#64748b' } }
    },
    yaxis: {
      labels: {
        style: { colors: isDarkTheme ? '#94a3b8' : '#64748b' },
        formatter: (val) => val.toFixed(0)
      }
    },
    grid: { borderColor: isDarkTheme ? '#334155' : '#f1f5f9' },
    theme: { mode: isDarkTheme ? 'dark' : 'light' },
    tooltip: { theme: isDarkTheme ? 'dark' : 'light' }
  }
})

const areaChartSeries = computed(() => [
  {
    name: 'Kunjungan Portal',
    data: stats.value.aktivitas_user?.map(u => u.kunjungan) || []
  },
  {
    name: 'Pengguna Aktif',
    data: stats.value.aktivitas_user?.map(u => u.aktif) || []
  }
])

// 3. Bar Chart (Category Breakdown) Configuration
const barChartOptions = computed(() => {
  const isDarkTheme = document.documentElement.classList.contains('dark')
  return {
    chart: {
      type: 'bar',
      toolbar: { show: false },
      background: 'transparent'
    },
    colors: ['#8B5CF6'], // Accent Purple
    plotOptions: {
      bar: {
        borderRadius: 5,
        horizontal: true,
        barHeight: '55%'
      }
    },
    xaxis: {
      categories: stats.value.per_bentuk?.map(b => b.kategori.replace('Inovasi ', '')) || [],
      labels: { style: { colors: isDarkTheme ? '#94a3b8' : '#64748b' } }
    },
    yaxis: {
      labels: { style: { colors: isDarkTheme ? '#94a3b8' : '#64748b' } }
    },
    grid: { borderColor: isDarkTheme ? '#334155' : '#f1f5f9' },
    theme: { mode: isDarkTheme ? 'dark' : 'light' },
    tooltip: { theme: isDarkTheme ? 'dark' : 'light' }
  }
})

const barChartSeries = computed(() => [{
  name: 'Jumlah',
  data: stats.value.per_bentuk?.map(b => b.total) || []
}])

// 4. Pie Chart (Status Distribution) Configuration
const pieChartOptions = computed(() => {
  const isDarkTheme = document.documentElement.classList.contains('dark')
  return {
    chart: {
      type: 'pie',
      background: 'transparent'
    },
    colors: ['#22C55E', '#F59E0B', '#EF4444'], // Success Green, Warning Orange, Danger Red
    labels: ['Disetujui', 'Pending', 'Ditolak'],
    stroke: { show: false },
    legend: {
      position: 'bottom',
      labels: { colors: isDarkTheme ? '#cbd5e1' : '#1e293b' }
    },
    theme: { mode: isDarkTheme ? 'dark' : 'light' },
    tooltip: { theme: isDarkTheme ? 'dark' : 'light' }
  }
})

const pieChartSeries = computed(() => [
  stats.value.approved || 0,
  stats.value.pending || 0,
  stats.value.rejected || 0
])

function formatCount(num) {
  if (num === undefined || num === null) return 0
  if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'k'
  }
  return num
}

let pollInterval = null

onMounted(async () => {
  await loadStats()
  // Trigger 5-second polling for real-time changes
  pollInterval = setInterval(loadStats, 5000)
})

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval)
  }
})
</script>
