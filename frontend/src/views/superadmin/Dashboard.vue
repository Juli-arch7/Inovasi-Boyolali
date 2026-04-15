<template>
  <div class="page fade-in">
    <div class="page-header">
      <h1>Super Admin Dashboard</h1>
      <p>Kelola administrator sistem Portal Inovasi Daerah</p>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-value">{{ admins.length }}</div>
        <div class="stat-label">Total Admin</div>
      </div>
    </div>

    <!-- Add Admin Form -->
    <div class="card" style="margin-bottom:2rem; max-width:600px">
      <h3 style="font-size:1.1rem; font-weight:600; margin-bottom:1rem">Tambah Admin Baru</h3>
      <div v-if="formMsg" :class="['alert', formError ? 'alert-error' : 'alert-success']">{{ formMsg }}</div>
      <form @submit.prevent="createAdmin">
        <div class="form-group">
          <label>Nama</label>
          <input class="form-control" v-model="form.name" required />
        </div>
        <div class="form-group">
          <label>Username</label>
          <input class="form-control" v-model="form.username" required />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input class="form-control" type="email" v-model="form.email" required />
        </div>
        <div class="form-group">
          <label>Password</label>
          <input class="form-control" type="password" v-model="form.password" required />
        </div>
        <div class="form-group">
          <label>Level</label>
          <select class="form-control" v-model="form.level">
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary" :disabled="submitting">
          {{ submitting ? 'Menyimpan...' : 'Tambah Admin' }}
        </button>
      </form>
    </div>

    <!-- Admin List -->
    <h3 style="font-size:1.1rem; font-weight:600; margin-bottom:1rem">Daftar Admin</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Level</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="admin in admins" :key="admin.id">
            <td>{{ admin.name }}</td>
            <td>{{ admin.email }}</td>
            <td><span class="badge badge-approved">{{ admin.role }}</span></td>
            <td>{{ admin.admin_profile?.level || '-' }}</td>
          </tr>
          <tr v-if="admins.length === 0">
            <td colspan="4" style="text-align:center; color:var(--text-muted)">Belum ada admin.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const admins = ref([])
const submitting = ref(false)
const formMsg = ref('')
const formError = ref(false)

const form = ref({
  name: '', username: '', email: '', password: '', level: 'admin'
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
  } catch (e) {
    formMsg.value = e.response?.data?.message || 'Gagal menambahkan admin.'
    formError.value = true
  } finally {
    submitting.value = false
  }
}

onMounted(loadAdmins)
</script>
