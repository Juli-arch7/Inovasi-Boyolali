<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />
    
    <main class="flex-1 p-6 sm:p-8 overflow-y-auto max-w-7xl mx-auto w-full">
      <!-- Page Header -->
      <div class="pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Daftar Produk Inovasi</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
          Kelola ketersediaan publik produk inovasi Kabupaten Boyolali yang telah disetujui.
        </p>
      </div>

      <!-- Filters & Search Bar Card -->
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/50 dark:border-slate-800 shadow-sm mb-6 transition-all duration-300">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
          <!-- Search -->
          <div class="w-full md:flex-1 relative flex items-center">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari berdasarkan nama inovasi..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
            />
          </div>

          <!-- Select filters -->
          <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 sm:flex-initial">
              <select v-model="filterOpd" class="w-full sm:w-56 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                <option value="">Semua Lembaga/OPD</option>
                <option v-for="opd in uniqueOpds" :key="opd" :value="opd">{{ opd }}</option>
              </select>
            </div>

            <div class="relative flex-1 sm:flex-initial">
              <select v-model="filterStatus" class="w-full sm:w-40 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800 shadow-sm overflow-hidden transition-all duration-300">
        <div class="overflow-x-auto w-full">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr class="bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200/50 dark:border-slate-800/50">
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider w-16 text-center">No</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider min-w-[200px]">Nama Inovasi</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Inisiator</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider min-w-[150px]">Lembaga / OPD</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Format</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider">Tahapan</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider text-center">Status</th>
                <th class="p-4 text-xs font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider text-center w-48">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/40">
              <tr 
                v-for="(product, index) in filteredProducts" 
                :key="product.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20 transition-colors"
              >
                <td class="p-4 text-sm font-semibold text-slate-500 dark:text-slate-400 text-center">{{ index + 1 }}</td>
                <td class="p-4">
                  <div class="text-sm font-bold text-slate-800 dark:text-white line-clamp-2 leading-snug">{{ product.nama_inovasi }}</div>
                  <div class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-1">Tahun {{ product.tahun_inovasi }}</div>
                </td>
                <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-400">{{ product.inisiator_profile?.nama_inisiator || '-' }}</td>
                <td class="p-4 text-sm font-semibold text-slate-600 dark:text-slate-400">{{ product.opd?.nama_opd || 'Umum' }}</td>
                <td class="p-4">
                  <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold text-primary dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/30">
                    {{ product.is_digital ? 'Digital' : 'Non-Digital' }}
                  </span>
                </td>
                <td class="p-4 text-sm font-medium text-slate-600 dark:text-slate-500">{{ product.tahapan_inovasi?.nama_tahapan || '-' }}</td>
                <td class="p-4 text-center">
                  <span 
                    class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold"
                    :class="product.is_active 
                      ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/10' 
                      : 'bg-rose-500 text-white shadow-sm shadow-rose-500/10'"
                  >
                    {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="p-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button 
                      @click="goToDetail(product.id)" 
                      class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    >
                      Detail
                    </button>
                    
                    <button
                      @click="toggleActive(product)"
                      :disabled="toggling === product.id"
                      class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors cursor-pointer disabled:opacity-50"
                      :class="product.is_active 
                        ? 'border-rose-200 dark:border-rose-900/40 text-rose-600 bg-rose-50 dark:bg-rose-950/20 hover:bg-rose-100' 
                        : 'border-emerald-200 dark:border-emerald-900/40 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/20 hover:bg-emerald-100'"
                    >
                      {{ product.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                  </div>
                </td>
              </tr>
              
              <!-- Empty state -->
              <tr v-if="filteredProducts.length === 0">
                <td colspan="8" class="p-10 text-center text-sm font-semibold text-slate-400">
                  Tidak ada data produk inovasi yang sesuai dengan pencarian Anda.
                </td>
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
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'
import { useToastStore } from '../../stores/toast'
import { Search, Clock, Loader2 } from 'lucide-vue-next'

const router = useRouter()
const toastStore = useToastStore()

const products = ref([])
const searchQuery = ref('')
const filterOpd = ref('')
const filterStatus = ref('')
const toggling = ref(null)

async function loadProducts() {
  try {
    const res = await api.get('/admin/products')
    products.value = res.data.filter(p => p.status_kurasi === 'approved')
  } catch (e) {
    console.error('Failed to load products', e)
    toastStore.show('Gagal memuat produk inovasi.', 'error')
  }
}

const uniqueOpds = computed(() => {
  const opds = products.value.map(p => p.opd?.nama_opd).filter(Boolean)
  return [...new Set(opds)]
})

const filteredProducts = computed(() => {
  return products.value.filter(product => {
    const matchesSearch = product.nama_inovasi?.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesOpd = !filterOpd.value || product.opd?.nama_opd === filterOpd.value
    const matchesStatus =
      filterStatus.value === '' ||
      (filterStatus.value === 'active' && product.is_active) ||
      (filterStatus.value === 'inactive' && !product.is_active)
    return matchesSearch && matchesOpd && matchesStatus
  })
})

function goToDetail(id) {
  // Tambahkan query parameter 'ref=products'
  router.push(`/admin/verifikasi/${id}?ref=products`)
}

async function toggleActive(product) {
  const confirmMsg = `Apakah Anda yakin ingin ${product.is_active ? 'menonaktifkan' : 'mengaktifkan'} produk "${product.nama_inovasi}"?`
  if (!confirm(confirmMsg)) return
  
  toggling.value = product.id
  try {
    const res = await api.put(`/admin/products/${product.id}/toggle-active`)
    
    // Update locally
    const idx = products.value.findIndex(p => p.id === product.id)
    if (idx !== -1) {
      products.value[idx] = { ...products.value[idx], is_active: res.data.product.is_active }
    }
    
    const statusText = res.data.product.is_active ? 'diaktifkan' : 'dinonaktifkan'
    toastStore.show(`Produk "${product.nama_inovasi}" berhasil ${statusText}.`, 'success')
  } catch (e) {
    console.error('Failed to toggle active state', e)
    toastStore.show(e.response?.data?.message || 'Gagal mengubah status produk.', 'error')
  } finally {
    toggling.value = null
  }
}

onMounted(() => {
  loadProducts()
})
</script>
