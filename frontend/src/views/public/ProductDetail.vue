<template>
<<<<<<< HEAD
  <div class="flex-1 bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Back Button -->
      <button 
        @click="$router.back()" 
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/60 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-all mb-8 shadow-sm cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
      >
        <ArrowLeft class="w-4 h-4" />
        Kembali
      </button>

      <!-- Loading State -->
      <div v-if="loading" class="py-20 flex flex-col items-center justify-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm">
        <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
        <p class="mt-4 text-sm font-medium text-slate-500">Memuat detail inovasi...</p>
=======
  <div class="product-detail-page container py-4">
    <div v-if="loading" class="text-center py-5">Memuat detail...</div>
    <div v-else-if="!product" class="text-center py-5">Produk tidak ditemukan.</div>
    <div v-else>
      <div class="detail-header mb-4">
        <button class="btn btn-outline btn-sm mb-2" @click="goBack">← Kembali</button>
        <h1 class="page-title">{{ product.nama_inovasi }}</h1>
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
      </div>

      <!-- Empty State -->
      <div v-else-if="!product" class="py-20 flex flex-col items-center justify-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm text-center">
        <Clock class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" />
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">Produk Tidak Ditemukan</h3>
        <p class="text-sm text-slate-500 max-w-xs mt-1">Inovasi yang Anda cari mungkin tidak aktif atau telah dihapus.</p>
        <button @click="$router.push('/')" class="mt-6 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-hover rounded-xl shadow-md transition-all cursor-pointer">
          Kembali ke Beranda
        </button>
      </div>

      <!-- Detail Layout -->
      <div v-else class="flex flex-col lg:flex-row gap-8 items-start">
        
        <!-- Left: Image Gallery & Description -->
        <div class="flex-1 w-full space-y-6">
          <!-- Main Image -->
          <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 overflow-hidden shadow-sm">
            <div class="relative h-[250px] sm:h-[350px] md:h-[450px] w-full bg-slate-100 dark:bg-slate-950/60">
              <img
                :src="mainImage"
                alt="Innovation Banner"
                class="w-full h-full object-cover"
                @error="handleImgError($event)"
              >
            </div>
            
            <!-- Thumbnail Gallery -->
            <div v-if="allMediaImages.length > 1" class="p-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800/60 flex gap-3 overflow-x-auto">
              <button
                v-for="(img, idx) in allMediaImages"
                :key="idx"
                @click="selectedImgIdx = idx"
                class="w-20 h-16 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0 cursor-pointer hover:scale-105 active:scale-95"
                :class="selectedImgIdx === idx ? 'border-primary shadow-md' : 'border-transparent opacity-70 hover:opacity-100'"
              >
                <img
                  :src="getImageUrl(img.isi_konten)"
                  :alt="'Foto ' + (idx + 1)"
                  class="w-full h-full object-cover"
                  @error="$event.target.style.display = 'none'"
                >
              </button>
            </div>
          </div>

          <!-- Description Section -->
          <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 p-6 sm:p-8 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
              <span class="w-1.5 h-6 rounded bg-primary"></span>
              Deskripsi Inovasi
            </h3>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base whitespace-pre-wrap">
              {{ product.deskripsi || 'Tidak ada deskripsi produk.' }}
            </p>
          </div>
        </div>

<<<<<<< HEAD
        <!-- Right: Administrative Sidebar -->
        <aside class="w-full lg:w-96 flex-shrink-0">
          <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 p-6 shadow-sm space-y-6">
            <div>
              <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-2">
                <ShieldCheck class="w-5 h-5 text-emerald-500" />
                Informasi Inovasi
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-500">
                Detail administrasi yang telah diverifikasi oleh admin Bapperida Boyolali.
              </p>
            </div>

            <hr class="border-slate-100 dark:border-slate-800/60" />

            <div class="space-y-4">
              <!-- Status -->
              <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Status Kurasi</span>
                <span 
                  class="px-2.5 py-1 rounded-lg text-xs font-bold text-white"
                  :class="{
                    'bg-emerald-500': product.status_kurasi === 'approved',
                    'bg-amber-500': product.status_kurasi === 'pending',
                    'bg-rose-500': product.status_kurasi === 'rejected',
                    'bg-slate-500': product.status_kurasi === 'draft' || !product.status_kurasi
                  }"
                >
                  {{ product.status_kurasi === 'approved' ? 'Disetujui' : (product.status_kurasi === 'pending' ? 'Pending' : 'Ditolak') }}
                </span>
=======
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
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
              </div>

              <!-- Tahapan -->
              <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Tahapan</span>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ product.tahapan_inovasi?.nama_tahapan || 'Inisiasi' }}</span>
              </div>

              <!-- Inisiator -->
              <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Inisiator</span>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-300">
                  <User class="w-4 h-4 text-slate-400" />
                  <span>{{ product.inisiator_profile?.nama_inisiator || 'Umum' }}</span>
                </div>
              </div>

              <!-- Kontak Inisiator -->
              <div v-if="product.inisiator_profile?.kontak" class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Kontak</span>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-300">
                  <Phone class="w-4 h-4 text-slate-400" />
                  <a :href="'tel:' + product.inisiator_profile.kontak" class="hover:text-primary transition-colors">
                    {{ product.inisiator_profile.kontak }}
                  </a>
                </div>
              </div>

              <!-- Kategori -->
              <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Format</span>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ product.is_digital ? 'Digital (Web/App)' : 'Non-Digital' }}</span>
              </div>

              <!-- OPD -->
              <div class="flex items-col flex-col gap-1 py-2 border-b border-slate-50 dark:border-slate-800/40">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Lembaga / OPD</span>
                <div class="flex items-start gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-300 mt-0.5">
                  <Building2 class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" />
                  <span>{{ product.opd?.nama_opd || 'Dinas Kabupaten Boyolali' }}</span>
                </div>
              </div>

              <!-- Tahun -->
              <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Tahun Peluncuran</span>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ product.tahun_inovasi }}</span>
              </div>

              <!-- Fitur 4: Kontak -->
              <div class="info-item" v-if="product.kontak">
                <label>Kontak</label>
                <span class="contact-info">
                  <i class='bx bx-phone'></i> {{ product.kontak }}
                </span>
              </div>
            </div>

<<<<<<< HEAD
            <!-- Stats counter display -->
            <div class="grid grid-cols-3 gap-3 bg-slate-50 dark:bg-slate-950 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
              <div class="text-center">
                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Views</div>
                <div class="text-base font-extrabold text-slate-800 dark:text-white mt-1">{{ views }}</div>
              </div>
              <div class="text-center">
                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Likes</div>
                <div class="text-base font-extrabold text-slate-800 dark:text-white mt-1">{{ likes }}</div>
              </div>
              <div class="text-center">
                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Downloads</div>
                <div class="text-base font-extrabold text-slate-800 dark:text-white mt-1">{{ downloads }}</div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
              <div class="flex gap-2">
                <button 
                  @click="handleLike" 
                  class="flex-1 flex items-center justify-center gap-2 h-11 rounded-xl text-sm font-semibold border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
                >
                  <Heart class="w-4 h-4" :class="isLiked ? 'fill-rose-500 text-rose-500' : 'text-slate-500'" />
                  {{ isLiked ? 'Liked' : 'Suka Inovasi' }}
                </button>

                <button 
                  @click="toggleFavorite" 
                  class="px-3 h-11 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
                  :title="isFavorited ? 'Hapus dari Favorit' : 'Tambah ke Favorit'"
                >
                  <Bookmark class="w-4 h-4" :class="isFavorited ? 'fill-primary text-primary' : 'text-slate-500'" />
                </button>
              </div>

              <!-- Marketplace / Download Link -->
              <div v-if="marketplaceLink">
                <a 
                  :href="marketplaceLink" 
                  target="_blank" 
                  rel="noopener" 
                  @click="handleDownload"
                  class="flex items-center justify-center gap-2 w-full h-11 rounded-xl font-semibold text-white bg-gradient-to-r from-primary to-accent hover:from-primary-hover hover:to-accent-hover shadow-md shadow-primary/20 transition-all hover:scale-[1.02] active:scale-[0.98] text-sm"
                >
                  <ExternalLink class="w-4 h-4" />
                  Lihat di Marketplace / Buka Link
                </a>
              </div>
              <div v-else>
                <button 
                  @click="handleDownloadFallback"
                  class="flex items-center justify-center gap-2 w-full h-11 rounded-xl font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all hover:scale-[1.02] active:scale-[0.98] text-sm cursor-pointer"
                >
                  <Download class="w-4 h-4" />
                  Unduh Dokumen Inovasi
                </button>
              </div>
=======
            <!-- Fitur 4: Link Marketplace -->
            <div v-if="marketplaceLink" class="marketplace-section mt-4">
              <a :href="marketplaceLink" target="_blank" rel="noopener" class="btn btn-primary w-full marketplace-btn">
                <i class='bx bx-store'></i> Lihat di Marketplace
              </a>
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
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
import { useToastStore } from '../../stores/toast'
import {
  ArrowLeft,
  Building2,
  Calendar,
  Eye,
  Heart,
  Download,
  ExternalLink,
  ShieldCheck,
  Clock,
  User,
  Bookmark,
  Phone
} from 'lucide-vue-next'

const route = useRoute()
<<<<<<< HEAD
const toastStore = useToastStore()

=======
const router = useRouter()
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
const product = ref(null)
const loading = ref(true)
const selectedImgIdx = ref(0)
const isLiked = ref(false)
const isFavorited = ref(false)

const views = ref(0)
const likes = ref(0)
const downloads = ref(0)

const BASE_URL = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace('/api', '')

// Detect if accessed from inisiator or public route
const isInisiatorRoute = computed(() => route.path.startsWith('/inisiator'))

function getImageUrl(path) {
  if (!path) return 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=800&auto=format&fit=crop'
  if (path.startsWith('http')) return path
  return `${BASE_URL}${path}`
}

function handleImgError(e) {
  e.target.src = 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=800&auto=format&fit=crop'
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
  if (allMediaImages.value.length === 0) return 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=800&auto=format&fit=crop'
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

<<<<<<< HEAD
async function handleLike() {
  if (isLiked.value) {
    toastStore.show('Anda sudah menyukai inovasi ini.', 'info')
    return
  }
  try {
    const res = await api.post(`/public/products/${product.value.id}/like`)
    if (res.data.success) {
      likes.value = res.data.likes_count
      isLiked.value = true
      toastStore.show('Terima kasih! Anda menyukai inovasi ini.', 'success')
      
      // Save like status in local storage for persistence
      const likedProducts = JSON.parse(localStorage.getItem('liked_products') || '[]')
      likedProducts.push(product.value.id)
      localStorage.setItem('liked_products', JSON.stringify(likedProducts))
    }
  } catch (e) {
    toastStore.show('Gagal mengirim feedback suka.', 'error')
  }
}

function toggleFavorite() {
  const favorites = JSON.parse(localStorage.getItem('favorites') || '[]')
  const index = favorites.indexOf(product.value.id)
  
  if (index >= 0) {
    favorites.splice(index, 1)
    isFavorited.value = false
    toastStore.show('Inovasi dihapus dari bookmark favorit Anda.', 'warning')
  } else {
    favorites.push(product.value.id)
    isFavorited.value = true
    toastStore.show('Inovasi disimpan ke bookmark favorit Anda.', 'success')
  }
  localStorage.setItem('favorites', JSON.stringify(favorites))
}

async function handleDownload() {
  try {
    const res = await api.post(`/public/products/${product.value.id}/download`)
    if (res.data.success) {
      downloads.value = res.data.downloads_count
    }
  } catch (e) {
    console.error('Failed to increment download count', e)
  }
}

async function handleDownloadFallback() {
  await handleDownload()
  toastStore.show('Mengunduh lampiran dokumen inovasi...', 'success')
}

onMounted(async () => {
  try {
    const res = await api.get(`/public/products/${route.params.id}`)
    product.value = res.data
    
    // Set counters
    views.value = product.value.views_count + 1 // Add local temporary increment for immediate render
    likes.value = product.value.likes_count
    downloads.value = product.value.downloads_count

    // Load status from local storage
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]')
    isFavorited.value = favorites.includes(product.value.id)

    const likedProducts = JSON.parse(localStorage.getItem('liked_products') || '[]')
    isLiked.value = likedProducts.includes(product.value.id)
=======
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
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
  } catch (e) {
    console.error('Failed to load product details', e)
    toastStore.show('Gagal memuat detail inovasi.', 'error')
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
/* Custom thumb gallery scrollbar */
.gallery-strip::-webkit-scrollbar {
  height: 6px;
}
<<<<<<< HEAD
.gallery-strip::-webkit-scrollbar-thumb {
  background: #e2e8f0;
=======

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
>>>>>>> 5f4bade7bf2c32223bf797b509a0f041f7bc15a8
  border-radius: 4px;
}
.dark .gallery-strip::-webkit-scrollbar-thumb {
  background: #334155;
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
