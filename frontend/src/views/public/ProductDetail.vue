<template>
  <div class="product-detail-page container py-4">
    <div v-if="loading" class="text-center py-5">Memuat detail...</div>
    <div v-else-if="!product" class="text-center py-5">Produk tidak ditemukan.</div>
    <div v-else>
      <div class="detail-header mb-4">
        <button class="btn btn-outline btn-sm mb-2" @click="goBack">← Kembali</button>
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
                <span :class="{
  'badge-success': product.status_kurasi === 'approved',
  'badge-warning': product.status_kurasi === 'pending',
  'badge-danger': product.status_kurasi === 'rejected'
}">
  {{ product.status_kurasi.toUpperCase() }}
</span>
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

              <!-- Fitur 4: Kontak -->
              <div class="info-item" v-if="product.kontak">
                <label>Kontak</label>
                <span class="contact-info">
                  <i class='bx bx-phone'></i> {{ product.kontak }}
                </span>
              </div>
            </div>

            <!-- Fitur 4: Link Marketplace -->
            <div v-if="marketplaceLink" class="marketplace-section mt-4">
              <a :href="marketplaceLink" target="_blank" rel="noopener" class="btn btn-primary w-full marketplace-btn">
                <i class='bx bx-store'></i> Lihat di Marketplace
              </a>
            </div>

            <!-- Fitur 4: Kontak link (WhatsApp / telepon) -->
            <div v-if="product.kontak" class="contact-section mt-3">
              <a :href="contactLink" target="_blank" rel="noopener" class="btn btn-outline w-full contact-btn">
                <i class='bx bx-message-dots'></i> Hubungi Inisiator
              </a>
            </div>

            <!-- Kontak Verifikator (Khusus untuk Inisiator) -->
            <div v-if="isInisiatorRoute && product.admin_profile" class="admin-contact-section mt-4 pt-4" style="border-top: 1px solid var(--border-color, #e5e7eb);">
              <h4 class="sidebar-title" style="font-size: 1rem; border-bottom: none; margin-bottom: 0.75rem; padding-bottom: 0;">Hubungi Verifikator</h4>
              <div class="info-list">
                <div class="info-item">
                  <label>Nama Admin</label>
                  <span>{{ product.admin_profile.nama_admin }}</span>
                </div>
                <div class="info-item" v-if="product.admin_profile.kontak">
                  <label>No. HP/WhatsApp</label>
                  <a :href="adminWaLink" target="_blank" rel="noopener" style="color: var(--primary); font-weight: 600; text-decoration: underline; font-size: 0.9rem;">
                    {{ product.admin_profile.kontak }}
                  </a>
                </div>
                <div class="info-item" v-if="product.admin_profile.user?.email">
                  <label>Email</label>
                  <a :href="`mailto:${product.admin_profile.user.email}`" style="color: var(--primary); font-weight: 600; text-decoration: underline; font-size: 0.9rem;">
                    {{ product.admin_profile.user.email }}
                  </a>
                </div>
              </div>
            </div>
            
            <!-- Fallback general admin contact if product has no specific admin verifier (e.g. pending) -->
            <div v-else-if="isInisiatorRoute && !product.admin_profile" class="admin-contact-section mt-4 pt-4" style="border-top: 1px solid var(--border-color, #e5e7eb);">
              <h4 class="sidebar-title" style="font-size: 1rem; border-bottom: none; margin-bottom: 0.75rem; padding-bottom: 0;">Hubungi Admin Bapperida</h4>
              <div class="info-list">
                <div class="info-item">
                  <label>Email Admin</label>
                  <a href="mailto:superadmin@inv.com" style="color: var(--primary); font-weight: 600; text-decoration: underline; font-size: 0.9rem;">
                    superadmin@inv.com
                  </a>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const router = useRouter()
const product = ref(null)
const loading = ref(true)
const selectedImgIdx = ref(0)

const BASE_URL = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace('/api', '')

// Detect if accessed from inisiator or public route
const isInisiatorRoute = computed(() => route.path.startsWith('/inisiator'))

function getImageUrl(path) {
  if (!path) return 'https://placehold.co/800x400/e2e8f0/64748b?text=No+Image'
  if (path.startsWith('http')) return path
  return `${BASE_URL}${path}`
}

function handleImgError(e) {
  e.target.src = 'https://placehold.co/800x400/e2e8f0/64748b?text=No+Image'
}

function goBack() {
  if (isInisiatorRoute.value) {
    router.push('/inisiator')
  } else {
    router.push('/')
  }
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
  // Cek dari kolom langsung
  if (product.value?.link_marketplace) return product.value.link_marketplace
  // Fallback: cek dari media_inovasi
  if (!product.value?.media_inovasi) return null
  const link = product.value.media_inovasi.find(m => m.jenis_media === 'link')
  return link?.isi_konten || null
})

// Generate contact link: jika angka -> wa.me, jika email -> mailto
const contactLink = computed(() => {
  const kontak = product.value?.kontak
  if (!kontak) return '#'
  // Check if it's a phone number (starts with 0 or + or contains mostly digits)
  const cleaned = kontak.replace(/[\s\-\(\)]/g, '')
  if (/^[\+]?[0-9]{8,}$/.test(cleaned)) {
    let waNumber = cleaned
    if (waNumber.startsWith('0')) {
      waNumber = '62' + waNumber.substring(1)
    }
    return `https://wa.me/${waNumber}`
  }
  if (kontak.includes('@')) {
    return `mailto:${kontak}`
  }
  return `https://wa.me/${cleaned}`
})

// Generate admin contact link
const adminWaLink = computed(() => {
  const kontak = product.value?.admin_profile?.kontak
  if (!kontak) return '#'
  const cleaned = kontak.replace(/[\s\-\(\)]/g, '')
  if (/^[\+]?[0-9]{8,}$/.test(cleaned)) {
    let waNumber = cleaned
    if (waNumber.startsWith('0')) {
      waNumber = '62' + waNumber.substring(1)
    }
    return `https://wa.me/${waNumber}`
  }
  return `https://wa.me/${cleaned}`
})

onMounted(async () => {
  try {
    if (isInisiatorRoute.value) {
      const res = await api.get(`/inisiator/products/${route.params.id}`)
      product.value = res.data
    } else {
      const res = await api.get(`/public/products/${route.params.id}`)
      product.value = res.data
    }
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

.contact-info {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.contact-info i {
  color: var(--primary);
}

/* Marketplace & Contact Buttons */
.marketplace-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.contact-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
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

.badge-warning {
  background: #fef3c7;
  color: #d97706;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  display: inline-block;
  width: fit-content;
}

.badge-danger {
  background: #fee2e2;
  color: #dc2626;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  display: inline-block;
  width: fit-content;
}
</style>
