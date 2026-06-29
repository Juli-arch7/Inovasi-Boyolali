<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />

    <main class="flex-1 p-6 sm:p-8 overflow-y-auto max-w-7xl mx-auto w-full">
      <!-- Page Header -->
      <div class="pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
          <History class="w-8 h-8 text-primary" />
          Log Aktivitas Admin
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
          <template v-if="isSuperAdmin">Pantau riwayat aksi yang dilakukan oleh seluruh administrator sistem.</template>
          <template v-else>Riwayat aksi pada produk inovasi yang dilakukan oleh administrator.</template>
        </p>
      </div>

      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm mb-6 transition-all duration-300">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
          <!-- Search -->
          <div class="w-full md:flex-1 relative flex items-center">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari admin, aksi, atau detail..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
            />
          </div>

          <!-- Select filters (superadmin only) -->
          <div v-if="isSuperAdmin" class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 sm:flex-initial">
              <select v-model="filterTargetType" class="w-full sm:w-48 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                <option value="">Semua Target</option>
                <option value="user">Pengguna / Admin</option>
                <option value="product">Produk Inovasi</option>
              </select>
            </div>
          </div>
          <!-- Admin badge (non-superadmin) -->
          <div v-else class="w-full md:w-auto">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 dark:bg-blue-950/30 text-primary border border-blue-200 dark:border-blue-900/30">
              Menampilkan log produk inovasi
            </span>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden transition-all duration-300">
        
        <!-- Loading Skeleton -->
        <div v-if="loading" class="p-6 space-y-4">
          <div v-for="i in 5" :key="i" class="flex items-center gap-4">
            <div class="flex-1 space-y-2">
              <div class="skeleton h-4 w-48 rounded-lg"></div>
              <div class="skeleton h-3 w-32 rounded-lg"></div>
            </div>
            <div class="skeleton h-6 w-24 rounded-full"></div>
            <div class="skeleton h-8 w-64 rounded-lg"></div>
          </div>
        </div>

        <div v-else class="overflow-x-auto w-full">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200/50 dark:border-slate-800/50">
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider min-w-[150px]">Waktu</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider min-w-[180px]">Administrator</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-center w-36">Aksi</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Detail Aktivitas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/40">
              <tr
                v-for="log in filteredLogs"
                :key="log.id"
                class="hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors"
              >
                <!-- Timestamp -->
                <td class="p-4 text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap font-medium">
                  {{ formatDateTime(log.created_at) }}
                </td>

                <!-- Administrator -->
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-slate-800 dark:text-white leading-tight">
                      {{ log.admin?.admin_profile?.nama_admin || log.admin?.name || 'Unknown' }}
                    </span>
                    <span 
                      class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider"
                      :class="log.admin?.role === 'superadmin' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400'"
                    >
                      {{ log.admin?.role === 'superadmin' ? 'Super Admin' : 'Admin' }}
                    </span>
                  </div>
                  <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono font-medium block mt-0.5">@{{ log.admin?.username }}</span>
                </td>

                <!-- Action Badge -->
                <td class="p-4 text-center">
                  <span
                    class="inline-flex items-center justify-center whitespace-nowrap px-2.5 py-1 rounded-lg text-[10px] font-bold border"
                    :class="getActionBadgeClass(log.action)"
                  >
                    {{ getActionLabel(log.action, log.description) }}
                  </span>
                </td>

                <!-- Description -->
                <td class="p-4 text-sm text-slate-600 dark:text-slate-300 font-medium">
                  {{ log.description }}
                </td>
              </tr>

              <!-- Empty state -->
              <tr v-if="filteredLogs.length === 0">
                <td colspan="4" class="p-16 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                      <History class="w-7 h-7 text-slate-300 dark:text-slate-600" />
                    </div>
                    <p class="text-sm font-semibold text-slate-400">Tidak ada log aktivitas ditemukan</p>
                    <p class="text-xs text-slate-300 dark:text-slate-600">Coba ubah kata kunci atau filter target</p>
                  </div>
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
import { ref, onMounted, computed } from 'vue'
import api from '../../services/api'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'
import { History, Search } from 'lucide-vue-next'

const auth = useAuthStore()
const toastStore = useToastStore()

const isSuperAdmin = computed(() => auth.userRole === 'superadmin')

const logs = ref([])
const loading = ref(true)
const searchQuery = ref('')
const filterTargetType = ref('')

async function loadLogs() {
  loading.value = true
  try {
    const res = await api.get('/admin/logs')
    logs.value = res.data || []
  } catch (e) {
    console.error('Failed to load logs', e)
    toastStore.show('Gagal memuat log aktivitas.', 'error')
  } finally {
    loading.value = false
  }
}

function formatDateTime(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  })
}

function getActionLabel(action, description = '') {
  switch (action) {
    case 'toggle_user_active':
    case 'toggle_admin_active':
      return /nonaktif/i.test(description) ? 'Nonaktifkan Akun' : 'Aktifkan Akun'
    case 'create_admin': return 'Tambah Admin'
    case 'verify_product': return 'Verifikasi Inovasi'
    case 'update_tahapan': return 'Update Tahapan'
    case 'toggle_product_active': return /nonaktif/i.test(description) ? 'Nonaktifkan Produk' : 'Aktifkan Produk'
    default: return action
  }
}

function getActionBadgeClass(action) {
  const map = {
    create_admin: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30',
    verify_product: 'bg-blue-50 text-primary border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30',
    update_tahapan: 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-indigo-900/30',
    toggle_user_active: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30',
    toggle_product_active: 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-850 dark:text-slate-300 dark:border-slate-700'
  }
  return map[action] || 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800'
}

const filteredLogs = computed(() => {
  return logs.value.filter(log => {
    const q = searchQuery.value.toLowerCase()
    
    const adminName = log.admin?.admin_profile?.nama_admin || log.admin?.name || ''
    const adminUsername = log.admin?.username || ''
    const actionLabel = getActionLabel(log.action, log.description)
    const desc = log.description || ''

    const matchesSearch = 
      adminName.toLowerCase().includes(q) ||
      adminUsername.toLowerCase().includes(q) ||
      actionLabel.toLowerCase().includes(q) ||
      desc.toLowerCase().includes(q)
      
    const matchesTargetType = !filterTargetType.value || log.target_type === filterTargetType.value

    return matchesSearch && matchesTargetType
  })
})

onMounted(loadLogs)
</script>

<style scoped>
.skeleton {
  background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}
.dark .skeleton {
  background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
  background-size: 200% 100%;
}
@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
