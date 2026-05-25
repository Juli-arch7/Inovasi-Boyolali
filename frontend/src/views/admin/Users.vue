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
                <th v-if="isSuperAdmin">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, index) in filteredUsers" :key="user.id">
                <td>{{ index + 1 }}</td>
                <td style="font-weight: 600;">{{ user.name || '-' }}</td>
                <td>{{ user.username }}</td>
                <td>{{ user.email }}</td>
                <td>
                  <span :class="['badge', `badge-${user.role}`]">
                    {{ user.role }}
                  </span>
                </td>
                <td v-if="isSuperAdmin">
                  <button 
                    v-if="user.id !== currentUserId"
                    class="btn btn-outline btn-sm delete-btn" 
                    @click="deleteUser(user.id)"
                  >
                    Hapus
                  </button>
                  <span v-else class="text-muted" style="font-size: 0.85rem; font-style: italic;">Anda</span>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td :colspan="isSuperAdmin ? 6 : 5" class="text-center py-4">Tidak ada pengguna ditemukan.</td>
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
const msg = ref('')
const isError = ref(false)

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
      (user.name && user.name.toLowerCase().includes(query)) ||
      user.username.toLowerCase().includes(query) ||
      user.email.toLowerCase().includes(query)
    
    const matchesRole = !filterRole.value || user.role === filterRole.value
    return matchesSearch && matchesRole
  })
})

async function deleteUser(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait juga akan terhapus.')) return
  
  msg.value = ''
  try {
    const res = await api.delete(`/admin/users/${id}`)
    msg.value = res.data.message || 'Pengguna berhasil dihapus.'
    isError.value = false
    await loadUsers()
  } catch (e) {
    msg.value = e.response?.data?.message || 'Gagal menghapus pengguna.'
    isError.value = true
  }
}

onMounted(loadUsers)
</script>

<style scoped>
.search-filter-wrapper {
  display: flex;
  gap: 1rem;
}
.search-input {
  flex: 2;
}
.filter-select {
  flex: 1;
  min-width: 200px;
}
.delete-btn {
  color: var(--danger);
  border-color: var(--danger);
}
.delete-btn:hover {
  background: var(--danger);
  color: #fff;
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
</style>
