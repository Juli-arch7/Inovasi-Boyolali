<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />

    <main class="flex-1 p-6 sm:p-8 overflow-y-auto max-w-7xl mx-auto w-full">

      <!-- Page Header -->
      <div class="pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Manajemen Administrator</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
              Kelola akun administrator dan tingkat akses sistem.
            </p>
          </div>
          <button
            @click="showAddForm = !showAddForm"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all cursor-pointer self-start"
            :class="showAddForm
              ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'
              : 'text-white bg-gradient-to-r from-primary to-blue-600 hover:from-blue-700 hover:to-blue-700 shadow-sm shadow-primary/30'"
          >
            <component :is="showAddForm ? X : UserPlus" class="w-4 h-4" />
            {{ showAddForm ? 'Tutup Form' : 'Tambah Admin Baru' }}
          </button>
        </div>
      </div>

      <!-- Add Admin Form -->
      <Transition name="slide-down">
        <div v-if="showAddForm" class="mb-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center">
              <UserPlus class="w-5 h-5 text-primary" />
            </div>
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Tambah Admin Baru</h2>
          </div>

          <!-- Alert in form -->
          <Transition name="fade">
            <div v-if="formMsg" class="mx-6 mt-5 flex items-start gap-3 px-4 py-3 rounded-xl" :class="formError ? 'bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40' : 'bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/40'">
              <component :is="formError ? AlertCircle : CheckCircle2" class="w-4 h-4 flex-shrink-0 mt-0.5" :class="formError ? 'text-rose-500' : 'text-emerald-500'" />
              <p class="text-sm font-medium" :class="formError ? 'text-rose-700 dark:text-rose-400' : 'text-emerald-700 dark:text-emerald-400'">{{ formMsg }}</p>
            </div>
          </Transition>

          <form @submit.prevent="createAdmin" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div class="form-field">
                <label class="field-label">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input class="field-input" v-model="form.name" placeholder="Nama Lengkap Admin" required />
              </div>
              <div class="form-field">
                <label class="field-label">Username <span class="text-rose-500">*</span></label>
                <input class="field-input" v-model="form.username" placeholder="username_admin" required />
              </div>
              <div class="form-field">
                <label class="field-label">Email <span class="text-rose-500">*</span></label>
                <input class="field-input" type="email" v-model="form.email" placeholder="email@admin.com" required />
              </div>
              <div class="form-field">
                <label class="field-label">Password <span class="text-rose-500">*</span></label>
                <input class="field-input" type="password" v-model="form.password" placeholder="••••••••" required />
              </div>
              <div class="form-field">
                <label class="field-label">Level Akses <span class="text-rose-500">*</span></label>
                <select class="field-input" v-model="form.level">
                  <option value="admin">Admin</option>
                  <option value="super_admin">Super Admin</option>
                </select>
              </div>
            </div>
            <div class="mt-6 flex justify-end">
              <button
                type="submit"
                :disabled="submitting"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-primary hover:bg-blue-700 transition-all disabled:opacity-50 cursor-pointer"
              >
                <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
                <Save v-else class="w-4 h-4" />
                {{ submitting ? 'Menyimpan...' : 'Simpan Admin' }}
              </button>
            </div>
          </form>
        </div>
      </Transition>

      <!-- Admin List Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Daftar Administrator</h2>
            <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500">{{ admins.length }} akun</span>
          </div>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="loading" class="p-6 space-y-4">
          <div v-for="i in 4" :key="i" class="flex items-center gap-4">
            <div class="skeleton w-10 h-10 rounded-full flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
              <div class="skeleton h-4 w-48 rounded-lg"></div>
              <div class="skeleton h-3 w-32 rounded-lg"></div>
            </div>
            <div class="skeleton h-6 w-20 rounded-full"></div>
            <div class="skeleton h-8 w-16 rounded-lg"></div>
          </div>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto w-full">
          <table class="w-full border-collapse text-left">
            <thead>
<<<<<<< HEAD
              <tr class="bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200/50 dark:border-slate-800/50">
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-14 text-center">No</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider min-w-[200px]">Administrator</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Username</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Email</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-center">Role</th>
                <th class="p-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-center w-28">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/40">
              <tr
                v-for="(admin, index) in admins"
                :key="admin.id"
                class="hover:bg-slate-50/70 dark:hover:bg-slate-950/30 transition-colors group"
              >
                <td class="p-4 text-sm font-semibold text-slate-500 dark:text-slate-400 text-center">{{ index + 1 }}</td>

                <!-- Name -->
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 shadow-sm"
                      :class="admin.admin_profile?.level === 'super_admin' ? 'bg-gradient-to-br from-amber-400 to-orange-500' : 'bg-gradient-to-br from-primary to-blue-600'">
                      {{ getInitials(admin.name || admin.username) }}
                    </div>
                    <div>
                      <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight">
                        {{ admin.name || '-' }}
                        <span v-if="admin.id === currentUserId" class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-primary/10 text-primary font-bold">Anda</span>
                      </p>
                      <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ admin.admin_profile?.level === 'super_admin' ? 'Super Admin' : 'Admin' }}
                      </p>
                    </div>
                  </div>
=======
              <tr>
                <th>Nama</th>
                <th>Username / Email</th>
                <th>Role</th>
                <th>Level</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="admin in admins" :key="admin.id" :class="{ 'row-inactive': admin.is_active === false }">
                <td style="font-weight: 600;">{{ admin.name }}</td>
                <td>
                  <div class="text-main">{{ admin.username }}</div>
                  <div class="text-light" style="font-size: 0.8rem;">{{ admin.email }}</div>
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
                </td>

                <td class="p-4">
                  <span class="text-sm font-mono font-semibold text-slate-600 dark:text-slate-300">@{{ admin.username }}</span>
                </td>

                <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-400">{{ admin.email }}</td>

                <td class="p-4 text-center">
                  <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold"
                    :class="admin.admin_profile?.level === 'super_admin'
                      ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/30'
                      : 'bg-blue-50 dark:bg-blue-950/30 text-primary dark:text-blue-400 border border-blue-200 dark:border-blue-900/30'"
                  >
                    <component :is="admin.admin_profile?.level === 'super_admin' ? Crown : Shield" class="w-3 h-3" />
                    {{ admin.admin_profile?.level === 'super_admin' ? 'Super Admin' : 'Admin' }}
                  </span>
                </td>
<<<<<<< HEAD

                <td class="p-4 text-center">
                  <button
                    v-if="admin.id !== currentUserId"
                    @click="openDeleteModal(admin)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border border-rose-200 dark:border-rose-900/40 text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/20 hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-all cursor-pointer opacity-0 group-hover:opacity-100"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                    Hapus
                  </button>
                  <span v-else class="text-xs text-slate-400 italic">—</span>
                </td>
              </tr>

              <!-- Empty state -->
              <tr v-if="admins.length === 0 && !loading">
                <td colspan="6" class="p-16 text-center">
                  <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                      <UserX class="w-7 h-7 text-slate-300 dark:text-slate-600" />
                    </div>
                    <p class="text-sm font-semibold text-slate-400">Belum ada administrator terdaftar</p>
                    <p class="text-xs text-slate-300 dark:text-slate-600">Tambahkan admin baru menggunakan form di atas</p>
                  </div>
                </td>
=======
                <td>
                  <span :class="['status-pill', admin.is_active !== false ? 'status-active' : 'status-inactive']">
                    {{ admin.is_active !== false ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td>
                  <div class="action-buttons" v-if="admin.id !== currentUserId">
                    <button 
                      :class="['btn', 'btn-sm', admin.is_active !== false ? 'btn-deactivate' : 'btn-activate']"
                      @click="toggleAdminActive(admin)"
                      :disabled="togglingAdmin === admin.id"
                    >
                      {{ admin.is_active !== false ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                  </div>
                  <span v-else class="text-muted" style="font-size: 0.85rem; font-style: italic;">Anda</span>
                </td>
              </tr>
              <tr v-if="admins.length === 0">
                <td colspan="6" class="text-center py-4">Belum ada admin yang terdaftar.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Admin Action Logs Card -->
      <div class="card mt-4">
        <h3 class="section-title mb-4" style="display: flex; align-items: center; gap: 0.5rem;">
          <i class='bx bx-history'></i> Log Aksi Administrator (Sistem)
        </h3>
        <div v-if="loadingLogs" class="text-center py-4 text-muted">Memuat log...</div>
        <div v-else-if="logs.length === 0" class="text-center py-4 text-muted">Belum ada log aktivitas admin.</div>
        <div v-else class="table-container">
          <table>
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Administrator</th>
                <th>Aksi</th>
                <th>Detail Aktivitas</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id">
                <td class="log-time" style="font-size: 0.85rem; color: var(--text-light); white-space: nowrap;">
                  {{ formatDateTime(log.created_at) }}
                </td>
                <td>
                  <span style="font-weight: 600;">{{ log.admin?.name || 'Unknown' }}</span>
                  <span :class="['badge', `badge-${log.admin?.role}`]" style="margin-left: 0.5rem; font-size: 0.7rem; padding: 0.15rem 0.4rem;">
                    {{ log.admin?.role }}
                  </span>
                </td>
                <td>
                  <span :class="['log-action-pill', `action-${log.action}`]">
                    {{ getActionLabel(log.action) }}
                  </span>
                </td>
                <td class="log-desc" style="font-size: 0.875rem; line-height: 1.4;">{{ log.description }}</td>
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer -->
        <div v-if="!loading && admins.length > 0" class="px-5 py-3 border-t border-slate-100 dark:border-slate-800/40 flex items-center justify-between">
          <p class="text-xs text-slate-400 font-medium">
            Total <span class="text-slate-600 dark:text-slate-300 font-bold">{{ admins.length }}</span> administrator
          </p>
        </div>
      </div>

    </main>

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showDeleteModal = false">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
          <div class="relative bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-slate-200/50 dark:border-slate-800/50 animate-scale-in">
            <div class="p-6 pb-4">
              <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center mb-4">
                <AlertTriangle class="w-6 h-6 text-rose-600" />
              </div>
              <h3 class="text-lg font-bold text-slate-900 dark:text-white">Hapus Administrator</h3>
              <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                Apakah Anda yakin ingin menghapus
                <span class="font-bold text-slate-800 dark:text-white">"{{ deleteTarget?.name || deleteTarget?.username }}"</span>?
                Tindakan ini tidak dapat dibatalkan.
              </p>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800/50 flex gap-3 justify-end">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer"
              >Batal</button>
              <button
                @click="confirmDelete"
                :disabled="deleting"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all cursor-pointer disabled:opacity-50"
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
import { useToastStore } from '../../stores/toast'
import api from '../../services/api'
import {
  UserPlus, X, Save, Trash2, UserX, Shield, Crown,
  AlertTriangle, AlertCircle, CheckCircle2, Loader2
} from 'lucide-vue-next'

const toastStore = useToastStore()

const admins = ref([])
const loading = ref(true)
const showAddForm = ref(false)
const submitting = ref(false)
const formMsg = ref('')
const formError = ref(false)
<<<<<<< HEAD
const showDeleteModal = ref(false)
const deleteTarget = ref(null)
const deleting = ref(false)
=======
const togglingAdmin = ref(null)

// Admin Logs
const logs = ref([])
const loadingLogs = ref(false)

async function loadLogs() {
  loadingLogs.value = true
  try {
    const res = await api.get('/admin/logs?target_type=user')
    logs.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loadingLogs.value = false
  }
}

function getActionLabel(action) {
  switch(action) {
    case 'toggle_user_active': return 'Toggle Aktif Akun';
    case 'create_admin': return 'Tambah Admin';
    case 'verify_product': return 'Verifikasi Inovasi';
    case 'update_tahapan': return 'Update Tahapan';
    default: return action;
  }
}

function formatDateTime(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8

const form = ref({
  name: '', username: '', email: '', password: '', level: 'admin'
})

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

async function loadAdmins() {
  loading.value = true
  try {
    const res = await api.get('/superadmin/admins')
    admins.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function createAdmin() {
  formMsg.value = ''
  submitting.value = true
  try {
    await api.post('/superadmin/admins', form.value)
    formMsg.value = 'Admin berhasil ditambahkan!'
    formError.value = false
    form.value = { name: '', username: '', email: '', password: '', level: 'admin' }
    await loadAdmins()
<<<<<<< HEAD
    setTimeout(() => { showAddForm.value = false; formMsg.value = '' }, 1500)
=======
    await loadLogs()
    showAddForm.value = false
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
  } catch (e) {
    formMsg.value = e.response?.data?.message || 'Gagal menambahkan admin.'
    formError.value = true
  } finally {
    submitting.value = false
  }
}

<<<<<<< HEAD
function openDeleteModal(admin) {
  deleteTarget.value = admin
  showDeleteModal.value = true
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    const res = await api.delete(`/superadmin/admins/${deleteTarget.value.id}`)
    toastStore.show(res.data.message || 'Admin berhasil dihapus.', 'success')
    showDeleteModal.value = false
    deleteTarget.value = null
=======
async function toggleAdminActive(admin) {
  const action = admin.is_active !== false ? 'menonaktifkan' : 'mengaktifkan'
  if (!confirm(`Apakah Anda yakin ingin ${action} administrator "${admin.name}"?`)) return
  
  togglingAdmin.value = admin.id
  try {
    const res = await api.put(`/admin/users/${admin.id}/toggle-active`)
    alert(res.data.message || `Admin berhasil di${action}.`)
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
    await loadAdmins()
    await loadLogs()
  } catch (e) {
<<<<<<< HEAD
    toastStore.show(e.response?.data?.message || 'Gagal menghapus admin.', 'error')
  } finally {
    deleting.value = false
=======
    alert(e.response?.data?.message || `Gagal ${action} admin.`)
  } finally {
    togglingAdmin.value = null
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
  }
}

onMounted(async () => {
  await loadAdmins()
  await loadLogs()
})
</script>

<style scoped>
.form-field {
  display: flex;
  flex-direction: column;
}

.field-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.5rem;
}
<<<<<<< HEAD

:is(.dark) .field-label {
  color: #94a3b8;
}

.field-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  background-color: #f8fafc;
  color: #0f172a;
  font-size: 0.875rem;
  transition: all 0.15s ease;
  outline: none;
=======
.row-inactive {
  opacity: 0.6;
  background: #fafafa;
}
.status-pill {
  padding: 0.25rem 0.75rem;
  border-radius: 99px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-block;
}
.status-active {
  background: #dcfce7;
  color: #16a34a;
}
.status-inactive {
  background: #fee2e2;
  color: #dc2626;
}
.btn-deactivate {
  background: transparent;
  color: #dc2626;
  border: 1px solid #dc2626;
  border-radius: 6px;
  padding: 0.3rem 0.75rem;
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-deactivate:hover:not(:disabled) {
  background: #fef2f2;
}
.btn-activate {
  background: transparent;
  color: #16a34a;
  border: 1px solid #16a34a;
  border-radius: 6px;
  padding: 0.3rem 0.75rem;
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-activate:hover:not(:disabled) {
  background: #f0fdf4;
}
.btn-deactivate:disabled,
.btn-activate:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.log-action-pill {
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-block;
}
.action-toggle_user_active {
  background: #fef3c7;
  color: #d97706;
  border: 1px solid #fcd34d;
}
.action-create_admin {
  background: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
}
.badge-superadmin {
  background: #fef3c7;
  color: #d97706;
}
.badge-admin {
  background: #e0f2fe;
  color: #0284c7;
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
}

.field-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  background-color: white;
}

:is(.dark) .field-input {
  background-color: rgba(15, 23, 42, 0.8);
  border-color: #334155;
  color: #e2e8f0;
}

:is(.dark) .field-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  background-color: #0f172a;
}

@keyframes scale-in {
  from { transform: scale(0.92); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.animate-scale-in { animation: scale-in 0.2s ease-out; }

.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}
.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
  max-height: 0;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
