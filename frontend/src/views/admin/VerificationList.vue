<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />

    <main class="flex-1 p-6 sm:p-8 overflow-y-auto max-w-7xl mx-auto w-full">
      <!-- Page Header -->
      <div class="pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Verifikasi Inovasi</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
              Kelola dan verifikasi ajuan inovasi yang masuk ke sistem.
            </p>
          </div>

          <!-- Quick Stats -->
          <div class="flex items-center gap-2">
            <div class="px-3 py-2 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200/50 dark:border-amber-900/30 text-center min-w-[70px]">
              <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Pending</p>
              <p class="text-lg font-extrabold text-amber-600 dark:text-amber-400">{{ statusCounts.pending }}</p>
            </div>
            <div class="px-3 py-2 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-200/50 dark:border-emerald-900/30 text-center min-w-[70px]">
              <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Disetujui</p>
              <p class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">{{ statusCounts.approved }}</p>
            </div>
            <div class="px-3 py-2 bg-rose-50 dark:bg-rose-950/30 rounded-xl border border-rose-200/50 dark:border-rose-900/30 text-center min-w-[70px]">
              <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Ditolak</p>
              <p class="text-lg font-extrabold text-rose-600 dark:text-rose-400">{{ statusCounts.rejected }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm mb-6 transition-all duration-300">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <!-- Search -->
          <div class="w-full md:flex-1 relative flex items-center">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama inovasi atau inisiator..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
            />
          </div>

          <!-- Status Pills -->
          <div class="w-full md:w-auto flex gap-2 flex-wrap">
            <button
              v-for="status in statusFilters"
              :key="status.value"
              @click="filterStatus = filterStatus === status.value ? '' : status.value"
              class="px-3.5 py-2 rounded-xl text-xs font-bold border transition-all cursor-pointer"
              :class="filterStatus === status.value
                ? status.activeClass
                : 'border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-950 hover:border-slate-300 dark:hover:border-slate-700'"
            >
              <component :is="status.icon" class="w-3.5 h-3.5 inline mr-1 -mt-0.5" />
              {{ status.label }}
            </button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden transition-all duration-300">

        <!-- Loading -->
        <div v-if="loading" class="p-6 space-y-4">
          <div v-for="i in 5" :key="i" class="flex items-center gap-4">
            <div class="skeleton w-8 h-8 rounded-lg flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
              <div class="skeleton h-4 w-56 rounded-lg"></div>
              <div class="skeleton h-3 w-36 rounded-lg"></div>
            </div>
            <div class="skeleton h-6 w-20 rounded-full"></div>
            <div class="skeleton h-8 w-20 rounded-lg"></div>
          </div>
        </div>

        <div v-else class="overflow-x-auto w-full">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200/50 dark:border-slate-800/50">
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider w-14 text-center">No</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider min-w-[250px]">Inovasi</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Inisiator</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider text-center">Kategori</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider text-center">Status</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider text-center w-32">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/40">
              <tr
                v-for="(product, index) in filteredProducts"
                :key="product.id"
                class="hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors group cursor-pointer"
                @click="goToDetail(product.id)"
              >
                <td class="p-4 text-sm font-semibold text-slate-500 dark:text-slate-400 text-center">{{ index + 1 }}</td>

                <!-- Innovation name + date -->
                <td class="p-4">
                  <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                      :class="getStatusBgLight(product.status_kurasi)">
                      <FileCheck class="w-4 h-4" :class="getStatusTextColor(product.status_kurasi)" />
                    </div>
                    <div>
                      <p class="text-sm font-bold text-slate-800 dark:text-white leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                        {{ product.nama_inovasi }}
                      </p>
                      <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 font-medium">
                        Tahun {{ product.tahun_inovasi }} · {{ product.opd?.nama_opd || 'Umum' }}
                      </p>
                    </div>
                  </div>
                </td>

                <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-400">
                  {{ product.inisiator_profile?.nama_inisiator || '-' }}
                </td>

                <td class="p-4 text-center">
                  <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold text-primary dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/30">
                    {{ product.is_digital ? 'Digital' : 'Non-Digital' }}
                  </span>
                </td>

                <td class="p-4 text-center">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold"
                    :class="getStatusBadgeClass(product.status_kurasi)"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotColor(product.status_kurasi)"></span>
                    {{ getStatusLabel(product.status_kurasi) }}
                  </span>
                </td>

                <td class="p-4 text-center" @click.stop>
                  <button
                    @click="goToDetail(product.id)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-primary hover:text-white hover:border-primary transition-all cursor-pointer"
                  >
                    <Eye class="w-3.5 h-3.5" />
                    Review
                  </button>
                </td>
              </tr>

              <!-- Empty -->
              <tr v-if="filteredProducts.length === 0 && !loading">
                <td colspan="6" class="p-16 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                      <ClipboardCheck class="w-7 h-7 text-slate-300 dark:text-slate-600" />
                    </div>
                    <p class="text-sm font-semibold text-slate-400">Belum ada ajuan inovasi</p>
                    <p class="text-xs text-slate-300 dark:text-slate-600">Ajuan yang masuk akan muncul di sini untuk diverifikasi</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer -->
        <div v-if="!loading && filteredProducts.length > 0" class="px-5 py-3 border-t border-slate-100 dark:border-slate-800/40 flex items-center justify-between">
          <p class="text-xs text-slate-400 font-medium">
            Menampilkan <span class="text-slate-600 dark:text-slate-300 font-bold">{{ filteredProducts.length }}</span> dari <span class="text-slate-600 dark:text-slate-300 font-bold">{{ products.length }}</span> inovasi
          </p>
        </div>
      </div>

      <!-- Verification Action Logs -->
      <div class="mt-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center">
            <Clock class="w-5 h-5 text-indigo-500" />
          </div>
          <h2 class="text-base font-bold text-slate-800 dark:text-white">Riwayat Aktivitas Verifikasi Inovasi</h2>
        </div>

        <div v-if="loadingLogs" class="p-8 flex justify-center">
          <Loader2 class="w-6 h-6 animate-spin text-slate-400" />
        </div>

        <div v-else-if="logs.length === 0" class="p-16 text-center">
          <p class="text-sm font-semibold text-slate-400">Belum ada riwayat aktivitas terkait verifikasi inovasi.</p>
        </div>

        <div v-else class="overflow-x-auto w-full">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200/50 dark:border-slate-800/50">
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Waktu</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Administrator</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Detail Aktivitas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/40">
              <tr v-for="log in logs" :key="log.id" class="hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors">
                <td class="p-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                  {{ formatDateTime(log.created_at) }}
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-slate-800 dark:text-white">
                      {{ log.admin?.admin_profile?.nama_admin || log.admin?.name || 'Admin' }}
                    </span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">
                      {{ log.admin?.role }}
                    </span>
                  </div>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center justify-center whitespace-nowrap min-w-max px-3 py-1 rounded-lg text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 leading-none">
                    {{ getActionLabel(log.action, log.description) }}
                  </span>
                </td>
                <td class="p-4 text-sm text-slate-600 dark:text-slate-400">
                  {{ log.description }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '../../stores/toast'
import api from '../../services/api'
import {
  Search, Eye, FileCheck, ClipboardCheck,
  Clock, CheckCircle2, XCircle, User
} from 'lucide-vue-next'

const router = useRouter()
const toastStore = useToastStore()

const products = ref([])
const searchQuery = ref('')
const filterStatus = ref('')
const loading = ref(true)
const logs = ref([])
const loadingLogs = ref(true)

const statusFilters = [
  { value: 'pending', label: 'Pending', icon: Clock, activeClass: 'bg-amber-500 text-white border-amber-500 shadow-sm shadow-amber-500/20' },
  { value: 'approved', label: 'Disetujui', icon: CheckCircle2, activeClass: 'bg-emerald-500 text-white border-emerald-500 shadow-sm shadow-emerald-500/20' },
  { value: 'rejected', label: 'Ditolak', icon: XCircle, activeClass: 'bg-rose-500 text-white border-rose-500 shadow-sm shadow-rose-500/20' },
]

const statusCounts = computed(() => {
  const counts = { pending: 0, approved: 0, rejected: 0 }
  products.value.forEach(p => {
    if (counts[p.status_kurasi] !== undefined) counts[p.status_kurasi]++
  })
  return counts
})

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const q = searchQuery.value.toLowerCase()
    const matchesSearch =
      p.nama_inovasi?.toLowerCase().includes(q) ||
      p.inisiator_profile?.nama_inisiator?.toLowerCase().includes(q)
    const matchesStatus = !filterStatus.value || p.status_kurasi === filterStatus.value
    return matchesSearch && matchesStatus
  })
})

function getStatusBadgeClass(status) {
  const map = {
    pending: 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/30',
    approved: 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/30',
    rejected: 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/30',
  }
  return map[status] || 'bg-slate-50 text-slate-600 border border-slate-200'
}

function getStatusBgLight(status) {
  const map = {
    pending: 'bg-amber-100 dark:bg-amber-950/30',
    approved: 'bg-emerald-100 dark:bg-emerald-950/30',
    rejected: 'bg-rose-100 dark:bg-rose-950/30',
  }
  return map[status] || 'bg-slate-100 dark:bg-slate-800'
}

function getStatusTextColor(status) {
  const map = {
    pending: 'text-amber-600 dark:text-amber-400',
    approved: 'text-emerald-600 dark:text-emerald-400',
    rejected: 'text-rose-600 dark:text-rose-400',
  }
  return map[status] || 'text-slate-500'
}

function getStatusDotColor(status) {
  const map = {
    pending: 'bg-amber-500 animate-pulse',
    approved: 'bg-emerald-500',
    rejected: 'bg-rose-500',
  }
  return map[status] || 'bg-slate-400'
}

function getStatusLabel(status) {
  const map = { pending: 'Pending', approved: 'Disetujui', rejected: 'Ditolak' }
  return map[status] || status
}

async function loadProducts() {
  loading.value = true
  try {
    const res = await api.get('/admin/products')
    products.value = res.data
  } catch (e) {
    console.error(e)
    toastStore.show('Gagal memuat data verifikasi.', 'error')
  } finally {
    loading.value = false
  }
}

async function loadLogs() {
  loadingLogs.value = true
  try {
    const res = await api.get('/admin/logs', { params: { target_type: 'product' } })
    logs.value = res.data || []
  } catch (e) {
    console.error('Failed to load verification logs', e)
  } finally {
    loadingLogs.value = false
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

function formatDateTime(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

function getActionLabel(action, description = '') {
  switch (action) {
    case 'verify_product': return 'Verifikasi Inovasi'
    case 'reject_product': return 'Tolak Inovasi'
    case 'update_tahapan': return 'Update Tahapan'
    case 'toggle_product_active': return /nonaktif/i.test(description) ? 'Nonaktif' : /aktif/i.test(description) ? 'Aktif' : 'Toggle Aktif Produk'
    default: return action
  }
}

function goToDetail(id) {
  router.push(`/admin/verifikasi/${id}`)
}

onMounted(() => {
  loadProducts()
  loadLogs()
})
</script>
