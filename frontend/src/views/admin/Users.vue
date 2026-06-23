<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4">
        <h1 class="page-title">Manajemen Pengguna</h1>
        <p class="text-muted">Kelola seluruh pengguna yang terdaftar di dalam sistem.</p>
      </div>

      <div class="card mb-4">
        <div class="search-filter-wrapper">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Cari nama, username, atau email..." 
            class="form-control search-input" 
          />
          <select v-model="filterRole" class="form-control filter-select">
            <option value="">Semua Role</option>
            <option value="superadmin">Super Admin</option>
            <option value="admin">Admin</option>
            <option value="inisiator">Inisiator</option>
          </select>
          <select v-model="filterStatus" class="form-control filter-select">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>
      </div>

      <div class="card">
        <div v-if="msg" :class="['alert', isError ? 'alert-error' : 'alert-success', 'mb-4']">{{ msg }}</div>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th v-if="isSuperAdmin">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in filteredUsers" :key="user.id" :class="{ 'row-inactive': !user.is_active }">
                <td>{{ index + 1 }}</td>
                <td style="font-weight: 600;">{{ user.name || '-' }}</td>
                <td>{{ user.username }}</td>
                <td>{{ user.email }}</td>
                <td>
                  <span :class="['badge', `badge-${user.role}`]">
                    {{ user.role }}
                  </span>
                </td>
                <td>
                  <span :class="['status-pill', user.is_active !== false ? 'status-active' : 'status-inactive']">
                    {{ user.is_active !== false ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td v-if="isSuperAdmin">
                  <div class="action-buttons" v-if="user.id !== currentUserId">
                    <!-- Tombol Nonaktifkan/Aktifkan -->
                    <button 
                      :class="['btn', 'btn-sm', user.is_active !== false ? 'btn-deactivate' : 'btn-activate']"
                      @click="toggleUserActive(user)"
                      :disabled="togglingUser === user.id"
                    >
                      {{ user.is_active !== false ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                  </div>
                  <span v-else class="text-muted" style="font-size: 0.85rem; font-style: italic;">Anda</span>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td :colspan="isSuperAdmin ? 7 : 6" class="text-center py-4">Tidak ada pengguna ditemukan.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Admin Action Logs Card -->
      <div class="card mt-4">
        <h3 class="section-title mb-4" style="display: flex; align-items: center; gap: 0.5rem;">
          <i class='bx bx-history'></i> Log Aksi Administrator (Akun)
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
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const auth = useAuthStore()
const users = ref([])
const searchQuery = ref('')
const filterRole = ref('')
const filterStatus = ref('')
const msg = ref('')
const isError = ref(false)
const togglingUser = ref(null)

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

const isSuperAdmin = computed(() => auth.userRole === 'superadmin')
const currentUserId = computed(() => {
  const userStr = localStorage.getItem('user')
  const userObj = userStr ? JSON.parse(userStr) : null
  return userObj?.id
})

async function loadUsers() {
  try {
    const res = await api.get('/admin/users')
    users.value = res.data
  } catch (e) {
    console.error(e)
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
    
    let matchesStatus = true
    if (filterStatus.value === 'active') {
      matchesStatus = user.is_active !== false
    } else if (filterStatus.value === 'inactive') {
      matchesStatus = user.is_active === false
    }
    
    return matchesSearch && matchesRole && matchesStatus
  })
})

async function toggleUserActive(user) {
  const action = user.is_active !== false ? 'menonaktifkan' : 'mengaktifkan'
  const warning = user.is_active !== false && user.role === 'inisiator' 
    ? '\n\n⚠️ Semua produk inovasi pengguna ini juga akan dinonaktifkan.' 
    : ''
  
  if (!confirm(`Apakah Anda yakin ingin ${action} pengguna "${user.name || user.username}"?${warning}`)) return
  
  togglingUser.value = user.id
  msg.value = ''
  try {
    const res = await api.put(`/admin/users/${user.id}/toggle-active`)
    msg.value = res.data.message || `Pengguna berhasil di${action}.`
    isError.value = false
    await loadUsers()
    await loadLogs()
  } catch (e) {
    msg.value = e.response?.data?.message || `Gagal ${action} pengguna.`
    isError.value = true
  } finally {
    togglingUser.value = null
  }
}

onMounted(async () => {
  await loadUsers()
  await loadLogs()
})
</script>

<style scoped>
.search-filter-wrapper {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}
.search-input {
  flex: 2;
  min-width: 200px;
}
.filter-select {
  flex: 1;
  min-width: 160px;
}

/* Action buttons in table */
.action-buttons {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.delete-btn {
  color: var(--danger);
  border-color: var(--danger);
}
.delete-btn:hover:not(:disabled) {
  background: var(--danger);
  color: #fff;
}

/* Status pills */
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

/* Inactive row styling */
.row-inactive {
  opacity: 0.6;
  background: #fafafa;
}

/* Toggle buttons */
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

.badge-superadmin {
  background: #fef3c7;
  color: #d97706;
}
.badge-admin {
  background: #e0f2fe;
  color: #0284c7;
}
.badge-inisiator {
  background: #dcfce7;
  color: #16a34a;
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
</style>
