<template>
  <div class="page fade-in">
    <div class="page-header">
      <h1>Dashboard Inisiator</h1>
      <p>Ajukan dan pantau status produk inovasi Anda</p>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-value">{{ myProducts.length }}</div>
        <div class="stat-label">Total Ajuan Saya</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ myProducts.filter(p => p.status_kurasi === 'approved').length }}</div>
        <div class="stat-label">Disetujui</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ myProducts.filter(p => p.status_kurasi === 'pending').length }}</div>
        <div class="stat-label">Menunggu</div>
      </div>
    </div>

    <!-- Submit Form -->
    <div class="card" style="margin-bottom:2rem; max-width:700px">
      <h3 style="font-size:1.1rem; font-weight:600; margin-bottom:1rem">Ajukan Produk Inovasi Baru</h3>
      <div v-if="formMsg" :class="['alert', formError ? 'alert-error' : 'alert-success']">{{ formMsg }}</div>
      <form @submit.prevent="submitProduct">
        <div class="form-group">
          <label>Nama Inovasi</label>
          <input class="form-control" v-model="form.nama_inovasi" required />
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea class="form-control" v-model="form.deskripsi"></textarea>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem">
          <div class="form-group">
            <label>Tahun Inovasi</label>
            <input class="form-control" type="number" v-model="form.tahun_inovasi" min="2000" max="2030" required />
          </div>
          <div class="form-group">
            <label>ID OPD</label>
            <input class="form-control" type="number" v-model="form.id_opd" required />
          </div>
          <div class="form-group">
            <label>ID Bentuk Inovasi</label>
            <input class="form-control" type="number" v-model="form.id_bentuk" required />
          </div>
          <div class="form-group">
            <label>ID Tahapan</label>
            <input class="form-control" type="number" v-model="form.id_tahapan" required />
          </div>
        </div>
        <div class="form-group">
          <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer">
            <input type="checkbox" v-model="form.is_digital" />
            Inovasi Digital
          </label>
        </div>
        <button type="submit" class="btn btn-primary" :disabled="submitting">
          {{ submitting ? 'Mengirim...' : 'Ajukan Inovasi' }}
        </button>
      </form>
    </div>

    <!-- My Products -->
    <h3 style="font-size:1.1rem; font-weight:600; margin-bottom:1rem">Produk Inovasi Saya</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nama Inovasi</th>
            <th>Tahun</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in myProducts" :key="product.id">
            <td style="font-weight:500">{{ product.nama_inovasi }}</td>
            <td>{{ product.tahun_inovasi }}</td>
            <td>
              <span :class="['badge', `badge-${product.status_kurasi}`]">
                {{ product.status_kurasi }}
              </span>
            </td>
          </tr>
          <tr v-if="myProducts.length === 0">
            <td colspan="3" style="text-align:center; color:var(--text-muted)">Belum ada ajuan.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const myProducts = ref([])
const submitting = ref(false)
const formMsg = ref('')
const formError = ref(false)

const form = ref({
  nama_inovasi: '', deskripsi: '', tahun_inovasi: new Date().getFullYear(),
  id_opd: '', id_bentuk: '', id_tahapan: '', is_digital: false
})

async function loadMyProducts() {
  try {
    const res = await api.get('/inisiator/products')
    myProducts.value = res.data
  } catch (e) {
    console.error(e)
  }
}

async function submitProduct() {
  formMsg.value = ''
  submitting.value = true
  try {
    await api.post('/inisiator/products', {
      ...form.value,
      is_digital: form.value.is_digital ? 1 : 0
    })
    formMsg.value = 'Produk inovasi berhasil diajukan!'
    formError.value = false
    form.value = {
      nama_inovasi: '', deskripsi: '', tahun_inovasi: new Date().getFullYear(),
      id_opd: '', id_bentuk: '', id_tahapan: '', is_digital: false
    }
    await loadMyProducts()
  } catch (e) {
    formMsg.value = e.response?.data?.message || 'Gagal mengajukan produk.'
    formError.value = true
  } finally {
    submitting.value = false
  }
}

onMounted(loadMyProducts)
</script>
