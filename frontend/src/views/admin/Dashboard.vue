<template>
  <div class="page fade-in">
    <div class="page-header">
      <h1>Admin Dashboard</h1>
      <p>Verifikasi dan kelola produk inovasi yang diajukan</p>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-value">{{ products.length }}</div>
        <div class="stat-label">Total Ajuan</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ products.filter(p => p.status_kurasi === 'pending').length }}</div>
        <div class="stat-label">Menunggu Review</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ products.filter(p => p.status_kurasi === 'approved').length }}</div>
        <div class="stat-label">Disetujui</div>
      </div>
    </div>

    <div v-if="actionMsg" :class="['alert', actionError ? 'alert-error' : 'alert-success']">{{ actionMsg }}</div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nama Inovasi</th>
            <th>Inisiator</th>
            <th>OPD</th>
            <th>Tahun</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td style="font-weight:500">{{ product.nama_inovasi }}</td>
            <td>{{ product.inisiator_profile?.nama_inisiator || '-' }}</td>
            <td>{{ product.opd?.nama_opd || '-' }}</td>
            <td>{{ product.tahun_inovasi }}</td>
            <td>
              <span :class="['badge', `badge-${product.status_kurasi}`]">
                {{ product.status_kurasi }}
              </span>
            </td>
            <td>
              <div style="display:flex; gap:0.4rem">
                <button class="btn btn-success btn-sm" @click="verify(product.id, 'approved')"
                  :disabled="product.status_kurasi === 'approved'">Setujui</button>
                <button class="btn btn-danger btn-sm" @click="verify(product.id, 'rejected')"
                  :disabled="product.status_kurasi === 'rejected'">Tolak</button>
              </div>
            </td>
          </tr>
          <tr v-if="products.length === 0">
            <td colspan="6" style="text-align:center; color:var(--text-muted)">Belum ada ajuan produk.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const products = ref([])
const actionMsg = ref('')
const actionError = ref(false)

async function loadProducts() {
  try {
    const res = await api.get('/admin/products')
    products.value = res.data
  } catch (e) {
    console.error(e)
  }
}

async function verify(id, status) {
  actionMsg.value = ''
  try {
    await api.put(`/admin/products/${id}/verify`, { status_kurasi: status })
    actionMsg.value = `Produk berhasil di-${status === 'approved' ? 'setujui' : 'tolak'}.`
    actionError.value = false
    await loadProducts()
  } catch (e) {
    actionMsg.value = e.response?.data?.message || 'Gagal mengubah status.'
    actionError.value = true
  }
}

onMounted(loadProducts)
</script>
