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
          <!-- Main Banner Image -->
          <div class="banner-card card mb-4">
            <img
              :src="mainImage"
              alt="Innovation Banner"
              class="banner-img"
              @error="handleImgError($event)"
            >
          </div>

          <!-- Thumbnail Gallery -->
          <div v-if="allMediaImages.length > 1" class="gallery-strip mb-4">
            <img
              v-for="(img, idx) in allMediaImages"
              :key="idx"
              :src="getImageUrl(img.isi_konten)"
              :alt="'Foto ' + (idx + 1)"
              :class="['thumb-img', { active: selectedImgIdx === idx }]"
              @click="selectedImgIdx = idx"
              @error="$event.target.style.display = 'none'"
            >
          </div>

          <div class="description-section card">
            <h3 class="section-title">Deskripsi Inovasi</h3>
            <p class="description-text">
              {{ product.deskripsi || 'Tidak ada deskripsi.' }}
            </p>
          </div>
        </div>

        <aside class="detail-sidebar">
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

            <!-- Marketplace Link -->
            <div v-if="marketplaceLink" class="mt-4">
              <a :href="marketplaceLink" target="_blank" rel="noopener" class="btn btn-primary w-full">
                <i class='bx bx-link-external'></i> Lihat di Marketplace
              </a>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const product = ref(null)
const loading = ref(true)
const selectedImgIdx = ref(0)

const BASE_URL = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace('/api', '')

function getImageUrl(path) {
  if (!path) return 'https://placehold.co/800x400/e2e8f0/64748b?text=No+Image'
  if (path.startsWith('http')) return path
  return `${BASE_URL}${path}`
}

function handleImgError(e) {
  e.target.src = 'https://placehold.co/800x400/e2e8f0/64748b?text=No+Image'
}

const allMediaImages = computed(() => {
  if (!product.value?.media_inovasi) return []
  return product.value.media_inovasi.filter(m => m.jenis_media !== 'link')
})

const mainImage = computed(() => {
  if (allMediaImages.value.length === 0) return 'https://placehold.co/800x400/e2e8f0/64748b?text=No+Image'
  return getImageUrl(allMediaImages.value[selectedImgIdx.value]?.isi_konten)
})

const marketplaceLink = computed(() => {
  if (!product.value?.media_inovasi) return null
  const link = product.value.media_inovasi.find(m => m.jenis_media === 'link')
  return link?.isi_konten || null
})

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
  display: block;
}

/* Thumbnail Gallery Strip */
.gallery-strip {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.thumb-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid transparent;
  cursor: pointer;
  transition: border-color 0.2s;
}

.thumb-img.active,
.thumb-img:hover {
  border-color: var(--primary);
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
