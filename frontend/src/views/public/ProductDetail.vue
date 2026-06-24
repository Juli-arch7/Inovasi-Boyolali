<template>
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
              </div>

              <!-- Tahapan -->
              <div class="flex items-center justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Tahapan</span>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ product.tahapan_inovasi?.nama_tahapan || 'Inisiasi' }}</span>
              </div>

              <!-- Status Tahapan (Khusus untuk Inisiator) -->
              <div v-if="isInisiatorRoute && product.status_tahapan" class="flex flex-col py-2 border-b border-slate-50 dark:border-slate-800/40 gap-1">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Status Tahapan</span>
                <span class="text-sm font-semibold text-primary dark:text-blue-400 bg-blue-50/50 dark:bg-blue-950/20 px-3 py-2 rounded-xl border border-blue-100 dark:border-blue-900/30">
                  {{ product.status_tahapan }}
                </span>
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
                <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Kontak Inisiator</span>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-300">
                  <Phone class="w-4 h-4 text-emerald-500" />
                  <a :href="'https://wa.me/' + product.inisiator_profile.kontak" target="_blank" rel="noopener" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 hover:underline transition-colors font-bold">
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

              <!-- Kontak Verifikator (Khusus untuk Inisiator) -->
              <div v-if="isInisiatorRoute && product.admin_profile" class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/60">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Hubungi Verifikator</h4>
                <div class="space-y-2">
                  <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-800/40">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase">Nama Admin</span>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ product.admin_profile.nama_admin }}</span>
                  </div>
                  <div v-if="product.admin_profile.kontak" class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-800/40">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase">No. WhatsApp</span>
                    <a :href="'https://wa.me/' + product.admin_profile.kontak" target="_blank" rel="noopener" class="text-sm font-bold text-primary hover:underline">
                      {{ product.admin_profile.kontak }}
                    </a>
                  </div>
                  <div v-if="product.admin_profile.user?.email" class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-800/40">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase">Email</span>
                    <a :href="'mailto:' + product.admin_profile.user.email" class="text-sm font-bold text-primary hover:underline">
                      {{ product.admin_profile.user.email }}
                    </a>
                  </div>
                </div>
              </div>
              
              <!-- Fallback general admin contact if product has no specific admin verifier (e.g. pending) -->
              <div v-else-if="isInisiatorRoute && !product.admin_profile" class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/60">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Hubungi Admin Bapperida</h4>
                <div class="flex items-center justify-between py-1">
                  <span class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase">Email Admin</span>
                  <a href="mailto:superadmin@inv.com" class="text-sm font-bold text-primary hover:underline">
                    superadmin@inv.com
                  </a>
                </div>
              </div>
            </div>

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
                  class="flex items-center justify-center gap-2 w-full h-11 rounded-xl font-bold text-white bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 shadow-md shadow-orange-500/20 transition-all hover:scale-[1.02] active:scale-[0.98] text-sm"
                >
                  <ExternalLink class="w-4 h-4" />
                  Kunjungi Marketplace
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
const toastStore = useToastStore()

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

const allMediaImages = computed(() => {
  if (!product.value?.media_inovasi) return []
  return product.value.media_inovasi.filter(m => m.jenis_media !== 'link')
})

const mainImage = computed(() => {
  if (allMediaImages.value.length === 0) return 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=800&auto=format&fit=crop'
  return getImageUrl(allMediaImages.value[selectedImgIdx.value]?.isi_konten)
})

const marketplaceLink = computed(() => {
  if (product.value?.link_marketplace) return product.value.link_marketplace
  if (!product.value?.media_inovasi) return null
  const link = product.value.media_inovasi.find(m => m.jenis_media === 'link')
  return link?.isi_konten || null
})

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
    const endpoint = isInisiatorRoute.value ? `/inisiator/products/${route.params.id}` : `/public/products/${route.params.id}`
    const res = await api.get(endpoint)
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
.gallery-strip::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 4px;
}
.dark .gallery-strip::-webkit-scrollbar-thumb {
  background: #334155;
}
</style>
