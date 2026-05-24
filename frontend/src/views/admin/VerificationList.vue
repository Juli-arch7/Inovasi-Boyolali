<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4">
        <h1 class="page-title">Verifikasi Inovasi</h1>
        <p class="text-muted">Daftar produk inovasi yang menunggu verifikasi.</p>
      </div>

      <div v-if="actionMsg" :class="['alert', actionError ? 'alert-error' : 'alert-success', 'mb-4']">{{ actionMsg }}</div>

      <div class="card">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Nama Inovasi</th>
                <th>Inisiator</th>
                <th>Tahun</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products" :key="product.id">
                <td style="font-weight: 600;">{{ product.nama_inovasi }}</td>
                <td>{{ product.inisiator_profile?.nama_inisiator || '-' }}</td>
                <td>{{ product.tahun_inovasi }}</td>
                <td>
                  <span :class="['badge', `badge-${product.status_kurasi}`]">
                    {{ product.status_kurasi }}
                  </span>
                </td>
                <td>
                  <div style="display: flex; gap: 0.4rem;">
                    <router-link :to="`/admin/verifikasi/${product.id}`" class="btn btn-outline btn-sm">Detail</router-link>
                    <button class="btn btn-success btn-sm" @click="verify(product.id, 'approved')"
                      :disabled="product.status_kurasi === 'approved'">Setujui</button>
                    <button class="btn btn-danger btn-sm" @click="verify(product.id, 'rejected')"
                      :disabled="product.status_kurasi === 'rejected'">Tolak</button>
                  </div>
                </td>
              </tr>
              <tr v-if="products.length === 0">
                <td colspan="5" class="text-center py-4">Belum ada ajuan produk.</td>
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
