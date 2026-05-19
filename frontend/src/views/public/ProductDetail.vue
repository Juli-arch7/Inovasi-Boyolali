<template>
  <div class="product-detail-page container py-4">
    <div v-if="loading" class="text-center py-5">Memuat detail...</div>
    <div v-else-if="!product" class="text-center py-5">Produk tidak ditemukan.</div>
    <div v-else>
      <div class="detail-header mb-4">
        <button class="btn btn-outline btn-sm mb-2" @click="$router.push('/')">← Kembali</button>
        <h1 class="page-title">{{ product.nama_inovasi }}</h1>
      </div>

      <div class="detail-layout">
        <div class="detail-main">
          <div class="banner-card card mb-4">
            <img :src="product.banner || 'https://via.placeholder.com/800x400'" alt="Innovation Banner" class="banner-img">
          </div>

          <div class="description-section card">
            <h3 class="section-title">Deskripsi Inovasi</h3>
            <p class="description-text">
              {{ product.deskripsi || 'Tidak ada deskripsi.' }}
            </p>
          </div>
        </div>

        <aside class="detail-sidebar">
          <div class="gallery-card card mb-4">
            <h3 class="sidebar-title">Galeri</h3>
            <div class="gallery-grid">
              <img src="https://via.placeholder.com/150" alt="Gallery 1">
              <img src="https://via.placeholder.com/150" alt="Gallery 2">
              <img src="https://via.placeholder.com/150" alt="Gallery 3">
            </div>
          </div>

          <div class="admin-info-card card">
            <h3 class="sidebar-title">Informasi Administratif</h3>
            <div class="info-list">
              <div class="info-item">
                <label>Status</label>
                <span class="badge-success">TERVERIFIKASI</span>
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
                <label>Kategori</label>
                <span>{{ product.is_digital ? 'Digital' : 'Non Digital' }}</span>
              </div>
              <div class="info-item">
                <label>OPD</label>
                <span>{{ product.opd?.nama_opd || '-' }}</span>
              </div>
              <div class="info-item">
                <label>Tahun</label>
                <span>{{ product.tahun_inovasi }}</span>
              </div>
            </div>
            <button class="btn btn-primary w-full mt-4">UNDUH PROPOSAL</button>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const product = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get(`/public/products/${route.params.id}`)
    product.value = res.data
  } catch (e) {
    console.error('Failed to load product', e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.py-4 { padding-top: 2rem; padding-bottom: 2rem; }

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

.banner-card {
  padding: 0;
  overflow: hidden;
}

.banner-img {
  width: 100%;
  height: 400px;
  object-fit: cover;
}

.section-title, .sidebar-title {
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

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
}

.gallery-grid img {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
  border-radius: 4px;
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

.badge-success {
  background: var(--primary-light);
  color: var(--primary);
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  display: inline-block;
  width: fit-content;
}
</style>
