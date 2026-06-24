<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />

    <main class="flex-1 p-6 sm:p-8 overflow-y-auto max-w-7xl mx-auto w-full">
      <!-- Page Header -->
      <div class="pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Manajemen Pengguna</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
            Kelola seluruh pengguna yang terdaftar di dalam sistem.
          </p>
        </div>
        <!-- Stats Summary -->
        <div class="flex items-center gap-3">
          <div class="px-4 py-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm text-center">
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total</p>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ users.length }}</p>
          </div>
          <div class="px-4 py-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm text-center">
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Hasil</p>
            <p class="text-xl font-extrabold text-primary">{{ filteredUsers.length }}</p>
          </div>
        </div>
      </div>

      <!-- Filters & Search Bar Card -->
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm mb-6 transition-all duration-300">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <!-- Search -->
          <div class="w-full md:flex-1 relative flex items-center">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama, username, atau email..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
            />
          </div>

          <!-- Role Filter -->
          <div class="w-full md:w-auto flex gap-3">
            <div class="relative flex-1 md:flex-initial">
              <Filter class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
              <select v-model="filterRole" class="w-full md:w-44 pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all appearance-none">
                <option value="">Semua Role</option>
                <option value="superadmin">Super Admin</option>
                <option value="admin">Admin</option>
                <option value="inisiator">Inisiator</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Active filters display -->
        <div v-if="searchQuery || filterRole" class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
          <span class="text-xs text-slate-400 font-medium">Filter aktif:</span>
          <span v-if="searchQuery" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold">
            "{{ searchQuery }}"
            <button @click="searchQuery = ''" class="ml-0.5 hover:opacity-70"><X class="w-3 h-3" /></button>
          </span>
          <span v-if="filterRole" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-accent/10 text-accent text-xs font-semibold">
            {{ filterRole }}
            <button @click="filterRole = ''" class="ml-0.5 hover:opacity-70"><X class="w-3 h-3" /></button>
          </span>
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden transition-all duration-300">

        <!-- Loading Skeleton -->
        <div v-if="loading" class="p-6 space-y-4">
          <div v-for="i in 6" :key="i" class="flex items-center gap-4">
            <div class="skeleton w-10 h-10 rounded-full flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
              <div class="skeleton h-4 w-48 rounded-lg"></div>
              <div class="skeleton h-3 w-32 rounded-lg"></div>
            </div>
            <div class="skeleton h-6 w-20 rounded-full"></div>
            <div class="skeleton h-8 w-16 rounded-lg"></div>
          </div>
        </div>

        <div v-else class="overflow-x-auto w-full">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200/50 dark:border-slate-800/50">
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider w-14 text-center">No</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider min-w-[220px]">Pengguna</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Username</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Email</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider text-center">Role</th>
                <th v-if="isSuperAdmin" class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider text-center w-28">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/40">
              <tr
                v-for="(user, index) in filteredUsers"
                :key="user.id"
                class="hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors group"
              >
                <td class="p-4 text-sm font-semibold text-slate-500 dark:text-slate-400 text-center">{{ index + 1 }}</td>

                <!-- User with Avatar -->
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <!-- Avatar Initials -->
                    <div
                      class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 shadow-sm"
                      :class="getAvatarClass(user.role)"
                    >
                      {{ getInitials(user.name || user.username) }}
                    </div>
                    <div>
                      <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight">
                        {{ user.name || '-' }}
                        <span v-if="user.id === currentUserId" class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-primary/10 text-primary font-bold">Anda</span>
                      </p>
                      <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Bergabung {{ formatDate(user.created_at) }}</p>
                    </div>
                  </div>
                </td>

                <td class="p-4">
                  <span class="text-sm font-mono font-semibold text-slate-600 dark:text-slate-300">@{{ user.username }}</span>
                </td>

                <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-400">{{ user.email }}</td>

                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold" :class="getRoleBadgeClass(user.role)">
                    <component :is="getRoleIcon(user.role)" class="w-3 h-3" />
                    {{ getRoleLabel(user.role) }}
                  </span>
                </td>

                <td v-if="isSuperAdmin" class="p-4 text-center">
                  <button
                    v-if="user.id !== currentUserId"
                    @click="openDeleteModal(user)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border border-rose-200 dark:border-rose-900/40 text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/20 hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-all cursor-pointer opacity-0 group-hover:opacity-100"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                    Hapus
                  </button>
                  <span v-else class="text-xs text-slate-400 italic">—</span>
                </td>
              </tr>

              <!-- Empty state -->
              <tr v-if="filteredUsers.length === 0 && !loading">
                <td :colspan="isSuperAdmin ? 6 : 5" class="p-16 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                      <Users class="w-7 h-7 text-slate-300 dark:text-slate-600" />
                    </div>
                    <p class="text-sm font-semibold text-slate-400">Tidak ada pengguna ditemukan</p>
                    <p class="text-xs text-slate-300 dark:text-slate-600">Coba ubah kata kunci atau filter pencarian</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer count -->
        <div v-if="!loading && filteredUsers.length > 0" class="px-5 py-3 border-t border-slate-100 dark:border-slate-800/40 flex items-center justify-between">
          <p class="text-xs text-slate-400 font-medium">
            Menampilkan <span class="text-slate-600 dark:text-slate-300 font-bold">{{ filteredUsers.length }}</span> dari <span class="text-slate-600 dark:text-slate-300 font-bold">{{ users.length }}</span> pengguna
          </p>
          <div class="flex items-center gap-1">
            <div v-for="role in ['superadmin','admin','inisiator']" :key="role" class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg" :class="getRoleBadgeClass(role)">
              <span class="text-[10px] font-bold">{{ getRoleLabel(role) }}: {{ users.filter(u => u.role === role).length }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- User Action Logs Card -->
      <div class="mt-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center">
            <History class="w-5 h-5 text-indigo-500" />
          </div>
          <h2 class="text-base font-bold text-slate-800 dark:text-white">Riwayat Aktivitas Pengguna</h2>
        </div>

        <div v-if="loadingLogs" class="p-8 flex justify-center">
          <Loader2 class="w-6 h-6 animate-spin text-slate-400" />
        </div>
        
        <div v-else-if="logs.length === 0" class="p-16 text-center">
          <p class="text-sm font-semibold text-slate-400">Belum ada riwayat aktivitas terkait pengguna.</p>
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
                    {{ getActionLabel(log.action) }}
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

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showDeleteModal = false">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
          <div class="relative bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200/50 dark:border-slate-800/50 animate-scale-in">
            <!-- Modal Header -->
            <div class="p-6 pb-4">
              <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center mb-4">
                <AlertTriangle class="w-6 h-6 text-rose-600" />
              </div>
              <h3 class="text-lg font-bold text-slate-900 dark:text-white">Hapus Pengguna</h3>
              <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                Apakah Anda yakin ingin menghapus pengguna
                <span class="font-bold text-slate-800 dark:text-white">"{{ deleteTarget?.name || deleteTarget?.username }}"</span>?
                Semua data terkait juga akan terhapus secara permanen.
              </p>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800/50 flex gap-3 justify-end">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer"
              >
                Batal
              </button>
              <button
                @click="confirmDelete"
                :disabled="deleting"
                class="px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all cursor-pointer disabled:opacity-50 inline-flex items-center gap-2"
              >
                <Loader2 v-if="deleting" class="w-4 h-4 animate-spin" />
                <Trash2 v-else class="w-4 h-4" />
                {{ deleting ? 'Menghapus...' : 'Ya, Hapus' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useToastStore } from '../../stores/toast'
import api from '../../services/api'
import {
  Search, Filter, X, Trash2, Users, AlertTriangle,
  Loader2, Shield, User, Building2, History
} from 'lucide-vue-next'

const auth = useAuthStore()
const toastStore = useToastStore()

const users = ref([])
const searchQuery = ref('')
const filterRole = ref('')
const loading = ref(true)
const showDeleteModal = ref(false)
const deleteTarget = ref(null)
const deleting = ref(false)
const logs = ref([])
const loadingLogs = ref(true)

const isSuperAdmin = computed(() => auth.userRole === 'superadmin')
const currentUserId = computed(() => {
  const userStr = localStorage.getItem('user')
  const userObj = userStr ? JSON.parse(userStr) : null
  return userObj?.id
})

function getInitials(name) {
  if (!name) return '?'
  const parts = name.split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.substring(0, 2).toUpperCase()
}

function getAvatarClass(role) {
  const map = {
    superadmin: 'bg-gradient-to-br from-amber-400 to-orange-500',
    admin: 'bg-gradient-to-br from-primary to-blue-600',
    inisiator: 'bg-gradient-to-br from-success to-emerald-600',
  }
  return map[role] || 'bg-gradient-to-br from-slate-400 to-slate-500'
}

function getRoleBadgeClass(role) {
  const map = {
    superadmin: 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/30',
    admin: 'bg-blue-50 dark:bg-blue-950/30 text-primary dark:text-blue-400 border border-blue-200 dark:border-blue-900/30',
    inisiator: 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/30',
  }
  return map[role] || 'bg-slate-50 text-slate-600 border border-slate-200'
}

function getRoleLabel(role) {
  const map = { superadmin: 'Super Admin', admin: 'Admin', inisiator: 'Inisiator' }
  return map[role] || role
}

function getRoleIcon(role) {
  const map = { superadmin: Shield, admin: Building2, inisiator: User }
  return map[role] || User
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
    case 'toggle_user_active':
    case 'toggle_admin_active':
      return /nonaktif/i.test(description) ? 'Nonaktif' : /aktif/i.test(description) ? 'Aktif' : 'Toggle Aktif Akun'
    case 'create_admin': return 'Tambah Admin'
    case 'verify_product': return 'Verifikasi Inovasi'
    case 'update_tahapan': return 'Update Tahapan'
    case 'toggle_product_active': return /nonaktif/i.test(description) ? 'Nonaktif' : /aktif/i.test(description) ? 'Aktif' : 'Toggle Aktif Produk'
    default: return action
  }
}

async function loadUsers() {
  loading.value = true
  try {
    const res = await api.get('/admin/users')
    users.value = res.data
  } catch (e) {
    console.error(e)
    toastStore.show('Gagal memuat data pengguna.', 'error')
  } finally {
    loading.value = false
  }
}

async function loadLogs() {
  loadingLogs.value = true
  try {
    const res = await api.get('/admin/logs', { params: { target_type: 'user' } })
    logs.value = res.data || []
  } catch (e) {
    console.error('Failed to load user logs', e)
  } finally {
    loadingLogs.value = false
  }
}

const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const query = searchQuery.value.toLowerCase()
    const matchesSearch =
      user.name?.toLowerCase().includes(query) ||
      user.username?.toLowerCase().includes(query) ||
      user.email?.toLowerCase().includes(query)
    const matchesRole = !filterRole.value || user.role === filterRole.value
    return matchesSearch && matchesRole
  })
})

function openDeleteModal(user) {
  deleteTarget.value = user
  showDeleteModal.value = true
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    const res = await api.delete(`/admin/users/${deleteTarget.value.id}`)
    toastStore.show(res.data.message || 'Pengguna berhasil dihapus.', 'success')
    showDeleteModal.value = false
    deleteTarget.value = null
    await loadUsers()
  } catch (e) {
    toastStore.show(e.response?.data?.message || 'Gagal menghapus pengguna.', 'error')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  loadUsers()
  loadLogs()
})
</script>

<style scoped>
@keyframes scale-in {
  from { transform: scale(0.92); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.animate-scale-in {
  animation: scale-in 0.2s ease-out;
}
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>
