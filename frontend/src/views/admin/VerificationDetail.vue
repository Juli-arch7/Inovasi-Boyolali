<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4">
        <h1 class="page-title">Detail Inovasi</h1>
        <router-link to="/admin/verifikasi" class="btn btn-outline">← Kembali</router-link>
      </div>

      <div v-if="loading" class="text-center py-5">Memuat data...</div>

      <div v-else-if="product" class="card">
        <h2 style="font-weight: 700; margin-bottom: 1rem;">{{ product.nama_inovasi }}</h2>
        <div class="detail-grid">
          <div class="detail-item">
            <span class="detail-label">Deskripsi</span>
            <p class="detail-value">{{ product.deskripsi || '-' }}</p>
          </div>
          <div class="detail-item">
            <span class="detail-label">Tahun Inovasi</span>
            <p class="detail-value">{{ product.tahun_inovasi }}</p>
          </div>
          <div class="detail-item">
            <span class="detail-label">Bentuk Inovasi</span>
            <p class="detail-value">{{ product.bentuk_inovasi?.nama_bentuk || '-' }}</p>
          </div>
          <div class="detail-item">
            <span class="detail-label">Status</span>
            <span :class="['badge', `badge-${product.status_kurasi}`]">{{ product.status_kurasi }}</span>
          </div>
        </div>

        <div v-if="actionMsg" :class="['alert', actionError ? 'alert-error' : 'alert-success', 'mt-4']">{{ actionMsg }}</div>

        <div class="mt-4" style="display: flex; gap: 0.5rem;">
          <button class="btn btn-success" @click="verify('approved')" :disabled="product.status_kurasi === 'approved'">Setujui</button>
          <button class="btn btn-danger" @click="verify('rejected')" :disabled="product.status_kurasi === 'rejected'">Tolak</button>
        </div>
      </div>

      <div v-else class="text-center py-5 text-muted">Produk tidak ditemukan.</div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const product = ref(null)
const loading = ref(true)
const actionMsg = ref('')
const actionError = ref(false)

async function loadProduct() {
  loading.value = true
  try {
    const res = await api.get(`/admin/products/${route.params.id}`)
    product.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function verify(status) {
  actionMsg.value = ''
  try {
    await api.put(`/admin/products/${route.params.id}/verify`, { status_kurasi: status })
    actionMsg.value = `Produk berhasil di-${status === 'approved' ? 'setujui' : 'tolak'}.`
    actionError.value = false
    await loadProduct()
  } catch (e) {
    actionMsg.value = e.response?.data?.message || 'Gagal mengubah status.'
    actionError.value = true
  }
}

onMounted(loadProduct)
</script>

<style scoped>
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.detail-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-light);
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.detail-value {
  font-size: 1rem;
  margin-top: 0.25rem;
}
</style>
