<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div v-if="loading" class="text-center py-5">Memuat detail...</div>
      <div v-else-if="!product" class="text-center py-5">Produk tidak ditemukan.</div>
      <div v-else>
        <div class="detail-header mb-4">
          <button class="btn btn-outline btn-sm mb-2" @click="$router.push('/admin/verifikasi')">← Kembali</button>
          <h1 class="page-title">Detail Verifikasi Inovasi</h1>
        </div>

        <div class="detail-layout">
          <div class="detail-main">
            <div class="card mb-4">
              <h2 class="section-title mb-4">{{ product.nama_inovasi }}</h2>
              <div class="banner-card mb-4" style="padding: 0; overflow: hidden; border-radius: 8px;">
                <img :src="product.banner || 'https://via.placeholder.com/800x400'" alt="Innovation Banner" style="width: 100%; height: 300px; object-fit: cover;">
              </div>
              <div class="description-section">
                <h3 class="sidebar-title">Deskripsi Inovasi</h3>
                <p class="description-text">
                  {{ product.deskripsi || 'Tidak ada deskripsi.' }}
                </p>
              </div>
            </div>
          </div>

          <aside class="detail-sidebar">
            <div class="admin-info-card card">
              <h3 class="sidebar-title">Informasi Administratif</h3>
              <div class="info-list">
                <div class="info-item">
                  <label>Status</label>
                  <span :class="['badge', `badge-${product.status_kurasi}`]">{{ product.status_kurasi }}</span>
                </div>
                <div class="info-item">
                  <label>Tahapan</label>
                  <span>{{ product.tahapan_inovasi?.nama_tahapan || '-' }}</span>
                </div>
                <div class="info-item">
                  <label>Inisiator</label>
                  <span>{{ product.inisiator_profile?.nama_inisiator || '-' }}</span>
                </div>
                <div class="info-item">
                  <label>OPD</label>
                  <span>{{ product.opd?.nama_opd || '-' }}</span>
                </div>
              </div>

              <div class="action-buttons mt-4">
                <button class="btn btn-primary w-full mb-2" @click="handleVerify('approved')" :disabled="submitting || product.status_kurasi === 'approved'">SETUJUI</button>
                <button class="btn btn-outline w-full" @click="handleVerify('rejected')" :disabled="submitting || product.status_kurasi === 'rejected'" style="color: var(--danger); border-color: var(--danger);">REJECT</button>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const router = useRouter()
const product = ref(null)
const loading = ref(true)
const submitting = ref(false)

async function loadProduct() {
  try {
    const res = await api.get(`/admin/products/${route.params.id}`)
    product.value = res.data
  } catch (e) {
    console.error('Failed to load product', e)
  } finally {
    loading.value = false
  }
}

async function handleVerify(status) {
  if (!confirm(`Apakah Anda yakin ingin ${status === 'approved' ? 'menyetujui' : 'menolak'} inovasi ini?`)) return
  
  submitting.value = true
  try {
    await api.put(`/admin/products/${route.params.id}/verify`, { status_kurasi: status })
    alert(`Inovasi berhasil di-${status === 'approved' ? 'setujui' : 'tolak'}.`)
    await loadProduct()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal mengubah status.')
  } finally {
    submitting.value = false
  }
}

onMounted(loadProduct)
</script>

<style scoped>
.detail-layout {
  display: flex;
  gap: 2rem;
}

.detail-main {
  flex: 2;
}

.detail-sidebar {
  flex: 1;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary);
}

.sidebar-title {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 1.25rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid var(--primary-light);
}

.description-text {
  line-height: 1.8;
  color: var(--text-muted);
}

.info-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-item {
  display: flex;
  flex-direction: column;
}

.info-item label {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-light);
  text-transform: uppercase;
  margin-bottom: 0.25rem;
}

.info-item span {
  font-size: 0.9375rem;
  font-weight: 500;
}
</style>
