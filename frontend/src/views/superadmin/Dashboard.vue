<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="header-with-action mb-4">
        <div class="page-header">
          <h1 class="page-title">Manajemen Administrator</h1>
          <p class="text-muted">Kelola akun administrator dan tingkat akses sistem.</p>
        </div>
        <button class="btn btn-primary" @click="showAddForm = !showAddForm">
          {{ showAddForm ? 'Tutup Form' : '+ Tambah Admin Baru' }}
        </button>
      </div>

      <!-- Add Admin Form -->
      <div v-if="showAddForm" class="card mb-4 fade-in">
        <h3 class="section-title mb-4">Tambah Admin Baru</h3>
        <div v-if="formMsg" :class="['alert', formError ? 'alert-error' : 'alert-success']">{{ formMsg }}</div>
        <form @submit.prevent="createAdmin">
          <div class="grid grid-cols-2">
            <div class="form-group">
              <label>Nama Lengkap</label>
              <input class="form-control" v-model="form.name" placeholder="Nama Lengkap Admin" required />
            </div>
            <div class="form-group">
              <label>Username</label>
              <input class="form-control" v-model="form.username" placeholder="username_admin" required />
            </div>
            <div class="form-group">
              <label>Email</label>
              <input class="form-control" type="email" v-model="form.email" placeholder="email@admin.com" required />
            </div>
            <div class="form-group">
              <label>Password</label>
              <input class="form-control" type="password" v-model="form.password" placeholder="••••••••" required />
            </div>
            <div class="form-group">
              <label>Level Akses</label>
              <select class="form-control" v-model="form.level">
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </div>
          </div>
          <div class="mt-4">
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              {{ submitting ? 'Menyimpan...' : 'Simpan Akun Admin' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Admin List -->
      <div class="card">
        <h3 class="section-title mb-4">Daftar Administrator</h3>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Nama</th>
                <th>Username / Email</th>
                <th>Role</th>
                <th>Level</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="admin in admins" :key="admin.id">
                <td style="font-weight: 600;">{{ admin.name }}</td>
                <td>
                  <div class="text-main">{{ admin.username }}</div>
                  <div class="text-light" style="font-size: 0.8rem;">{{ admin.email }}</div>
                </td>
                <td><span class="badge-custom">{{ admin.role }}</span></td>
                <td>
                  <span :class="['badge', admin.admin_profile?.level === 'super_admin' ? 'badge-success' : 'badge-pending']">
                    {{ admin.admin_profile?.level || 'admin' }}
                  </span>
                </td>
                <td>
                  <button 
                    v-if="admin.id !== currentUserId" 
                    class="btn btn-outline btn-sm delete-btn" 
                    @click="deleteAdmin(admin.id)"
                  >
                    Hapus
                  </button>
                  <span v-else class="text-muted" style="font-size: 0.85rem; font-style: italic;">Anda</span>
                </td>
              </tr>
              <tr v-if="admins.length === 0">
                <td colspan="5" class="text-center py-4">Belum ada admin yang terdaftar.</td>
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

const admins = ref([])
const showAddForm = ref(false)
const submitting = ref(false)
const formMsg = ref('')
const formError = ref(false)

const form = ref({
  name: '', username: '', email: '', password: '', level: 'admin'
})

const currentUserId = computed(() => {
  const userStr = localStorage.getItem('user')
  const userObj = userStr ? JSON.parse(userStr) : null
  return userObj?.id
})

async function loadAdmins() {
  try {
    const res = await api.get('/superadmin/admins')
    admins.value = res.data
  } catch (e) {
    console.error(e)
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
    showAddForm.value = false
  } catch (e) {
    formMsg.value = e.response?.data?.message || 'Gagal menambahkan admin.'
    formError.value = true
  } finally {
    submitting.value = false
  }
}

async function deleteAdmin(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus administrator ini?')) return
  try {
    const res = await api.delete(`/superadmin/admins/${id}`)
    alert(res.data.message || 'Admin berhasil dihapus.')
    await loadAdmins()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menghapus admin.')
  }
}

onMounted(loadAdmins)
</script>

<style scoped>
.header-with-action {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--primary);
}

.badge-custom {
  background: var(--primary-light);
  color: var(--primary);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.delete-btn {
  color: var(--danger);
  border-color: var(--danger);
}
.delete-btn:hover {
  background: var(--danger);
  color: #fff;
}
</style>
