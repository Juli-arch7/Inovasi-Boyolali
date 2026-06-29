<template>
  <div class="flex-1 bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300 pb-16">
    <!-- Hero Section -->
    <section class="relative overflow-hidden py-20 bg-gradient-to-br from-blue-600/10 via-purple-600/5 to-cyan-500/10 dark:from-blue-950/20 dark:via-purple-950/15 dark:to-cyan-950/15 border-b border-slate-200/40 dark:border-slate-800/40">
      <!-- Background SVG mesh elements -->
      <div class="absolute inset-0 pointer-events-none opacity-40">
        <svg class="absolute top-0 right-0 w-[600px] h-[600px] text-primary/10 dark:text-primary/5 transform translate-x-1/4 -translate-y-1/4" fill="currentColor" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="40" />
        </svg>
        <svg class="absolute bottom-0 left-0 w-[450px] h-[450px] text-secondary/15 dark:text-secondary/5 transform -translate-x-1/4 translate-y-1/4" fill="currentColor" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="30" />
        </svg>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <!-- Badge -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-primary dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 border border-blue-200/50 dark:border-blue-900/30 mb-6 animate-pulse">
          <Sparkles class="w-3.5 h-3.5" />
          <span>PORTAL INOVASI RESMI BOYOLALI</span>
        </div>

        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
          Temukan <span class="bg-gradient-to-r from-primary via-accent to-secondary bg-clip-text text-transparent">Inovasi Terbaik</span><br/>
          Kabupaten Boyolali
        </h1>
        <p class="mt-4 max-w-2xl mx-auto text-base sm:text-lg text-slate-600 dark:text-slate-400 font-medium">
          Mendorong pembangunan daerah yang kreatif, kolaboratif, dan transparan melalui integrasi program pelayanan publik dan tata kelola modern.
        </p>

        <!-- Large Search Bar -->
        <div class="mt-10 max-w-2xl mx-auto">
          <div class="glass p-2 rounded-2xl shadow-xl flex items-center gap-2 border border-slate-200/80 dark:border-slate-800/80 focus-within:ring-2 focus-within:ring-primary/20 dark:focus-within:ring-primary/10 transition-all duration-300">
            <Search class="w-5 h-5 text-slate-500 dark:text-slate-500 ml-3 flex-shrink-0" />
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Cari inovasi, dinas, atau bentuk pelayanan..." 
              class="w-full bg-transparent border-none outline-none py-2 px-1 text-slate-800 dark:text-slate-200 placeholder-slate-500 dark:placeholder-slate-500 text-sm sm:text-base"
            />
            <button class="px-5 py-2.5 rounded-xl font-semibold text-white bg-gradient-to-r from-primary to-accent hover:from-primary-hover hover:to-accent-hover shadow-md shadow-primary/15 hover:shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98] text-sm sm:text-base flex-shrink-0">
              CARI
            </button>
          </div>
        </div>

        <!-- Category Pills -->
        <div class="mt-8 flex items-center justify-start md:justify-center gap-2.5 overflow-x-auto no-scrollbar pb-3 px-4 -mx-4 md:mx-0 md:overflow-x-visible md:pb-0 md:px-0 scroll-smooth">
          <button 
            v-for="cat in categories" 
            :key="cat.value"
            @click="filters.category = cat.value"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold border transition-all duration-200 cursor-pointer flex-shrink-0"
            :class="filters.category === cat.value 
              ? 'bg-primary border-primary text-white shadow-md shadow-primary/20 scale-[1.03]' 
              : 'bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'"
          >
            {{ cat.label }}
          </button>
        </div>
      </div>
    </section>

    <!-- Main Content Layout -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
      <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Filter Sidebar -->
        <aside class="w-full lg:w-72 flex-shrink-0">
          <div class="sticky top-20 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800 p-6 shadow-sm shadow-slate-100 dark:shadow-none transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-2">
                <SlidersHorizontal class="w-5 h-5 text-primary" />
                <h3 class="font-bold text-slate-800 dark:text-slate-200">Filter Pencarian</h3>
              </div>
              <button 
                @click="resetFilters" 
                class="text-xs font-semibold text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-primary transition-colors cursor-pointer"
              >
                Reset
              </button>
            </div>
            
            <hr class="border-slate-100 dark:border-slate-800 mb-6" />

            <div class="space-y-5">
              <!-- Kategori -->
              <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Kategori</label>
                <select v-model="filters.category" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                  <option value="">Semua Kategori</option>
                  <option value="Pelayanan Publik">Pelayanan Publik</option>
                  <option value="Tata Kelola Pemerintahan">Tata Kelola Pemerintahan</option>
                  <option value="Inovasi Daerah Lainnya">Inovasi Daerah Lainnya</option>
                </select>
              </div>

              <!-- Kecamatan -->
              <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Kecamatan</label>
                <select v-model="filters.kecamatan" @change="filters.kelurahan = ''" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                  <option value="">Pilih Kecamatan</option>
                  <option v-for="kec in metadata.kecamatans" :key="kec.id" :value="kec.nama_kecamatan">{{ kec.nama_kecamatan }}</option>
                </select>
              </div>

              <!-- Kelurahan -->
              <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Kelurahan</label>
                <select v-model="filters.kelurahan" :disabled="!filters.kecamatan" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                  <option value="">Pilih Kelurahan</option>
                  <option v-for="kel in filteredKelurahansOptions" :key="kel.id" :value="kel.nama_kelurahan">{{ kel.nama_kelurahan }}</option>
                </select>
              </div>

              <!-- Tahun -->
              <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Tahun</label>
                <select v-model="filters.tahun" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                  <option value="">Pilih Tahun</option>
                  <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                </select>
              </div>

              <!-- Bentuk Inovasi -->
              <div class="flex flex-col gap-2">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Bentuk Inovasi</label>
                <div class="flex flex-col gap-2.5 mt-1">
                  <label class="inline-flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                    <input type="checkbox" v-model="filters.bentuk" value="digital" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-primary focus:ring-primary/20">
                    <span>Digital</span>
                  </label>
                  <label class="inline-flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                    <input type="checkbox" v-model="filters.bentuk" value="non-digital" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-primary focus:ring-primary/20">
                    <span>Non-Digital</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <!-- Products Grid Area -->
        <main class="flex-1">
          <!-- Skeleton Loading -->
          <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            <div v-for="n in 6" :key="n" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 overflow-hidden shadow-sm flex flex-col h-[400px]">
              <div class="skeleton h-48 w-full"></div>
              <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                  <div class="skeleton h-4 w-1/4 rounded mb-4"></div>
                  <div class="skeleton h-6 w-3/4 rounded mb-2"></div>
                  <div class="skeleton h-4 w-full rounded mb-2"></div>
                  <div class="skeleton h-4 w-5/6 rounded"></div>
                </div>
                <div class="flex justify-between items-center pt-4 border-t border-slate-100 dark:border-slate-800/60">
                  <div class="skeleton h-4 w-1/3 rounded"></div>
                  <div class="skeleton h-8 w-1/4 rounded-lg"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredProducts.length === 0" class="text-center py-20 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm flex flex-col items-center">
            <SlidersHorizontal class="w-12 h-12 text-slate-300 dark:text-slate-700 mb-4 animate-bounce" />
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-300">Tidak Ada Produk Inovasi</h3>
            <p class="text-slate-500 dark:text-slate-500 text-sm mt-1 max-w-sm">
              Coba sesuaikan kata kunci pencarian Anda atau hapus beberapa filter untuk menemukan produk.
            </p>
            <button @click="resetFilters" class="mt-6 px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary-hover rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
              Atur Ulang Filter
            </button>
          </div>

          <!-- Products Grid -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-6 animate-fade-in">
            <div 
              v-for="product in filteredProducts" 
              :key="product.id"
              class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 overflow-hidden shadow-sm hover:shadow-xl hover:scale-[1.02] hover:border-primary/50 dark:hover:border-primary/50 transition-all duration-300 flex flex-col h-[450px]"
            >
              <!-- Card Image & Header Badges -->
              <div class="relative h-48 w-full bg-slate-100 dark:bg-slate-950/60 overflow-hidden flex-shrink-0 cursor-pointer" @click="goToDetail(product.id)">
                <img
                  :src="getProductImage(product)"
                  alt="Innovation Thumbnail"
                  @error="handleImgError($event)"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                  <span class="text-white text-xs font-semibold flex items-center gap-1.5">
                    Lihat detail inovasi <ArrowRight class="w-3.5 h-3.5" />
                  </span>
                </div>
                
                <!-- Status & Category Badges -->
                <div class="absolute top-3 left-3 right-3 flex items-center justify-between">
                  <span 
                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm"
                    :class="{
                      'bg-emerald-500': product.status_kurasi === 'approved',
                      'bg-amber-500': product.status_kurasi === 'pending',
                      'bg-rose-500': product.status_kurasi === 'rejected',
                      'bg-slate-500': product.status_kurasi === 'draft' || !product.status_kurasi
                    }"
                  >
                    {{ product.status_kurasi === 'approved' ? 'Disetujui' : (product.status_kurasi === 'pending' ? 'Pending' : (product.status_kurasi === 'rejected' ? 'Ditolak' : 'Draft')) }}
                  </span>
                  
                  <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-primary dark:text-blue-400 bg-white/95 dark:bg-slate-900/95 shadow-sm border border-slate-200/50 dark:border-slate-800/50">
                    {{ product.is_digital ? 'Digital' : 'Non-Digital' }}
                  </span>
                </div>
              </div>

              <!-- Card Body -->
              <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                  <div class="flex items-center gap-2 mb-2 text-xs font-semibold text-slate-500 dark:text-slate-500">
                    <Calendar class="w-3.5 h-3.5" />
                    <span>{{ product.tahun_inovasi }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                    <span class="truncate max-w-[120px]">{{ product.bentuk_inovasi?.nama_bentuk || 'Inovasi' }}</span>
                  </div>

                  <h3 @click="goToDetail(product.id)" class="text-base font-bold text-slate-800 dark:text-white line-clamp-2 hover:text-primary dark:hover:text-primary cursor-pointer transition-colors leading-snug mb-2">
                    {{ product.nama_inovasi }}
                  </h3>
                  
                  <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed mb-4">
                    {{ product.deskripsi || 'Tidak ada deskripsi produk.' }}
                  </p>
                </div>

                <!-- Footer Stats & OPD -->
                <div class="-mt-1 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                  <div class="flex items-center gap-2 mb-2.5 text-xs text-slate-500 dark:text-slate-400">
                    <Building2 class="w-4 h-4 text-slate-400 flex-shrink-0" />
                    <span class="truncate font-semibold">{{ product.opd?.nama_opd || 'Inisiator Umum' }}</span>
                  </div>
                  
                  <div class="flex items-center justify-end">

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">                      
                      <button 
                        @click="goToDetail(product.id)"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-hover shadow-sm hover:shadow shadow-primary/10 transition-all cursor-pointer hover:translate-x-0.5"
                      >
                        Detail
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'
import { useToastStore } from '../../stores/toast'
import { 
  Search, 
  SlidersHorizontal, 
  Calendar, 
  Building2, 
  Heart, 
  ArrowRight, 
  Sparkles 
} from 'lucide-vue-next'

const router = useRouter()
const toastStore = useToastStore()

const products = ref([])
const loading = ref(true)
const searchQuery = ref('')
const favorites = ref(JSON.parse(localStorage.getItem('favorites') || '[]'))

const filters = ref({
  category: '',
  kecamatan: '',
  kelurahan: '',
  tahun: '',
  bentuk: [],
  sort: 'newest'
})

const categories = [
  { label: 'Semua Inovasi', value: '' },
  { label: 'Pelayanan Publik', value: 'Pelayanan Publik' },
  { label: 'Tata Kelola Pemerintahan', value: 'Tata Kelola Pemerintahan' },
  { label: 'Inovasi Daerah Lainnya', value: 'Inovasi Daerah Lainnya' }
]

const metadata = ref({
  kecamatans: [],
  kelurahans: []
})

const filteredKelurahansOptions = computed(() => {
  if (!filters.value.kecamatan) return metadata.value.kelurahans
  const kec = metadata.value.kecamatans.find(k => k.nama_kecamatan === filters.value.kecamatan)
  if (kec) {
    return metadata.value.kelurahans.filter(k => k.id_kecamatan === kec.id)
  }
  return metadata.value.kelurahans
})

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    // 1. Search Query
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase()
      const matchName = p.nama_inovasi?.toLowerCase().includes(q)
      const matchDesc = p.deskripsi?.toLowerCase().includes(q)
      const matchOpd = p.opd?.nama_opd && p.opd.nama_opd.toLowerCase().includes(q)
      if (!matchName && !matchDesc && !matchOpd) return false
    }

    // 2. Kategori
    if (filters.value.category) {
      const bName = p.bentuk_inovasi?.nama_bentuk || ''
      if (filters.value.category === 'Pelayanan Publik' && !bName.includes('Pelayanan Publik')) return false
      if (filters.value.category === 'Tata Kelola Pemerintahan' && !bName.includes('Tata Kelola')) return false
      if (filters.value.category === 'Inovasi Daerah Lainnya' && (bName.includes('Pelayanan Publik') || bName.includes('Tata Kelola'))) return false
    }

    // 3. Kecamatan
    if (filters.value.kecamatan) {
      const matchKecName = p.inisiator_profile?.kelurahan?.kecamatan?.nama_kecamatan === filters.value.kecamatan
      const matchKecId = p.inisiator_profile?.kelurahan?.kecamatan?.id === Number(filters.value.kecamatan)
      if (!matchKecName && !matchKecId) return false
    }

    // 4. Kelurahan
    if (filters.value.kelurahan) {
      const matchKelName = p.inisiator_profile?.kelurahan?.nama_kelurahan === filters.value.kelurahan
      const matchKelId = p.inisiator_profile?.kelurahan?.id === Number(filters.value.kelurahan)
      if (!matchKelName && !matchKelId) return false
    }

    // 5. Tahun
    if (filters.value.tahun) {
      if (p.tahun_inovasi !== Number(filters.value.tahun)) return false
    }

    // 6. Bentuk (Digital / Non-Digital Checkbox)
    if (filters.value.bentuk && filters.value.bentuk.length > 0) {
      const isDig = !!p.is_digital
      const wantsDigital = filters.value.bentuk.includes('digital')
      const wantsNonDigital = filters.value.bentuk.includes('non-digital')
      if (wantsDigital && !wantsNonDigital && !isDig) return false
      if (!wantsDigital && wantsNonDigital && isDig) return false
    }

    return true
  })
})

const years = computed(() => {
  const currentYear = new Date().getFullYear();
  const list = [];
  for (let y = currentYear; y >= 2018; y--) {
    list.push(y);
  }
  return list;
})

onMounted(async () => {
  try {
    const res = await api.get('/public/products')
    products.value = res.data
  } catch (e) {
    console.error('Failed to load products', e)
    toastStore.show('Gagal memuat produk inovasi.', 'error')
  } finally {
    loading.value = false
  }

  try {
    const resMeta = await api.get('/public/metadata')
    metadata.value = resMeta.data
  } catch (e) {
    console.error('Failed to load metadata', e)
  }
})

function resetFilters() {
  filters.value = {
    category: '',
    kecamatan: '',
    kelurahan: '',
    tahun: '',
    bentuk: [],
    sort: 'newest'
  }
  searchQuery.value = ''
  toastStore.show('Filter pencarian telah diatur ulang.', 'info')
}

function goToDetail(id) {
  router.push(`/products/${id}`)
}

function getProductImage(product) {
  if (!product.media_inovasi || product.media_inovasi.length === 0) {
    return 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600&auto=format&fit=crop'
  }
  const primary = product.media_inovasi.find(m => m.is_primary && m.jenis_media !== 'link')
  const firstPhoto = product.media_inovasi.find(m => m.jenis_media !== 'link')
  const media = primary || firstPhoto || product.media_inovasi[0]
  if (!media || !media.isi_konten) return 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600&auto=format&fit=crop'
  if (media.isi_konten.startsWith('http')) return media.isi_konten
  return `http://localhost:8000${media.isi_konten}`
}

function handleImgError(e) {
  e.target.src = 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600&auto=format&fit=crop'
}

function isFavorited(id) {
  return favorites.value.includes(id)
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
