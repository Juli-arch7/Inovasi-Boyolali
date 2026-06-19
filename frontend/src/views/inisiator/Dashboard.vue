<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />

    <main class="flex-1 p-6 sm:p-8 overflow-y-auto max-w-7xl mx-auto w-full">

      <!-- Page Header -->
      <div class="pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Inovasi Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
              Kelola dan pantau semua inovasi yang telah Anda ajukan.
            </p>
          </div>
          <button
            @click="$router.push('/inisiator/pengajuan')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-blue-600 hover:from-blue-700 hover:to-blue-700 shadow-sm shadow-primary/30 transition-all cursor-pointer self-start"
          >
            <Plus class="w-4 h-4" />
            Ajukan Inovasi Baru
          </button>
        </div>
      </div>

      <!-- Rejection Notifications -->
      <TransitionGroup name="slide-fade" tag="div" class="space-y-3 mb-6">
        <div
          v-for="product in rejectedProducts"
          :key="'notif-' + product.id"
          class="relative bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40 border-l-4 border-l-rose-500 rounded-2xl p-5 overflow-hidden"
        >
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center flex-shrink-0">
              <XCircle class="w-5 h-5 text-rose-600" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                <h4 class="text-sm font-bold text-rose-700 dark:text-rose-400">Pengajuan Inovasi Ditolak</h4>
                <span v-if="product.tanggal_review" class="text-xs text-slate-400 font-medium">
                  {{ formatDate(product.tanggal_review) }}
                </span>
              </div>
              <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">🔖 {{ product.nama_inovasi }}</p>
              <div class="bg-rose-100/60 dark:bg-rose-950/30 rounded-xl p-3 mb-3">
                <p class="text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-1">Alasan Penolakan:</p>
                <p class="text-sm text-rose-700 dark:text-rose-400/80 leading-relaxed">
                  {{ product.alasan_penolakan || 'Tidak ada alasan yang diberikan.' }}
                </p>
              </div>
              <button
                @click="editProduct(product.id)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all cursor-pointer"
              >
                <RefreshCw class="w-3.5 h-3.5" />
                Ajukan Ulang
              </button>
            </div>
          </div>
          <button
            @click="dismissNotification(product.id)"
            class="absolute top-3 right-3 w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-all cursor-pointer"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>
      </TransitionGroup>

      <!-- Filter Tabs -->
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <!-- Tabs -->
          <div class="flex gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl flex-wrap">
            <button
              v-for="tab in tabs"
              :key="tab"
              @click="activeTab = tab"
              class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer"
              :class="activeTab === tab
                ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm'
                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
            >
              {{ tab }}
              <span
                v-if="tab !== 'Semua'"
                class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold"
                :class="activeTab === tab ? 'bg-primary/10 text-primary' : 'bg-slate-200 dark:bg-slate-700 text-slate-400'"
              >
                {{ getTabCount(tab) }}
              </span>
            </button>
          </div>

          <!-- Search -->
          <div class="w-full md:flex-1 relative flex items-center">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama inovasi..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
            />
          </div>
        </div>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="loading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 p-5">
          <div class="flex items-start gap-4">
            <div class="skeleton w-12 h-12 rounded-xl flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
              <div class="skeleton h-5 w-64 rounded-lg"></div>
              <div class="skeleton h-3 w-40 rounded-lg"></div>
              <div class="skeleton h-3 w-full rounded-lg mt-3"></div>
              <div class="skeleton h-3 w-3/4 rounded-lg"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Innovation List -->
      <div v-else class="space-y-4">
        <TransitionGroup name="fade-up">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group"
          >
            <div class="p-5">
              <div class="flex items-start gap-4">
                <!-- Status Icon -->
                <div
                  class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                  :class="getStatusBgLight(product.status_kurasi)"
                >
                  <component :is="getStatusIcon(product.status_kurasi)" class="w-6 h-6" :class="getStatusTextColor(product.status_kurasi)" />
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                      <h3 class="text-base font-bold text-slate-800 dark:text-white leading-snug group-hover:text-primary transition-colors">
                        {{ product.nama_inovasi }}
                      </h3>
                      <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 font-medium">
                        Tahun {{ product.tahun_inovasi }}
                        <span v-if="product.is_digital" class="ml-1.5 px-1.5 py-0.5 rounded bg-primary/10 text-primary text-[10px] font-bold">Digital</span>
                      </p>
                    </div>
                    <span
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold flex-shrink-0"
                      :class="getStatusBadgeClass(product.status_kurasi)"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotColor(product.status_kurasi)"></span>
                      {{ getStatusLabel(product.status_kurasi) }}
                    </span>
                  </div>

                  <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed line-clamp-2">
                    {{ product.deskripsi || 'Tidak ada deskripsi.' }}
                  </p>
                </div>
              </div>

              <!-- Footer -->
              <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/40 flex items-center justify-between gap-3">
                <p class="text-xs text-slate-400 font-medium">
                  <span v-if="product.opd?.nama_opd">{{ product.opd.nama_opd }}</span>
                </p>
                <div class="flex gap-2">
                  <button
                    @click="goToDetail(product.id)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-primary hover:text-white hover:border-primary transition-all cursor-pointer"
                  >
                    <Eye class="w-3.5 h-3.5" />
                    Detail
                  </button>
                  <button
                    @click="editProduct(product.id)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all cursor-pointer"
                  >
                    <Pencil class="w-3.5 h-3.5" />
                    Edit
                  </button>
                </div>
              </div>
            </div>
          </div>
        </TransitionGroup>

        <!-- Empty State -->
        <div v-if="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-20 gap-4">
          <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
            <FileSearch class="w-8 h-8 text-slate-300 dark:text-slate-600" />
          </div>
          <div class="text-center">
            <p class="text-sm font-semibold text-slate-500">Belum ada inovasi ditemukan</p>
            <p class="text-xs text-slate-400 dark:text-slate-600 mt-1">
              {{ searchQuery ? 'Coba kata kunci lain' : 'Mulai ajukan inovasi pertama Anda!' }}
            </p>
          </div>
          <button
            v-if="!searchQuery"
            @click="$router.push('/inisiator/pengajuan')"
            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-xl text-white bg-primary hover:bg-blue-700 transition-all cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            Ajukan Sekarang
          </button>
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
import {
  Plus, Search, Eye, Pencil, XCircle, X, RefreshCw,
  Clock, CheckCircle2, AlertCircle, FileSearch, FileText
} from 'lucide-vue-next'

const router = useRouter()
const myProducts = ref([])
const loading = ref(true)
const activeTab = ref('Semua')
const tabs = ['Semua', 'Draft', 'Verifikasi', 'Selesai']
const searchQuery = ref('')
const dismissedIds = ref(new Set())

const rejectedProducts = computed(() =>
  myProducts.value.filter(p => p.status_kurasi === 'rejected' && !dismissedIds.value.has(p.id))
)

function dismissNotification(id) {
  dismissedIds.value = new Set([...dismissedIds.value, id])
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
}

function getTabCount(tab) {
  return myProducts.value.filter(p => {
    if (tab === 'Draft') return p.status_kurasi === 'draft'
    if (tab === 'Verifikasi') return p.status_kurasi === 'pending'
    if (tab === 'Selesai') return p.status_kurasi === 'approved'
    return true
  }).length
}

const filteredProducts = computed(() => {
  return myProducts.value.filter(p => {
    const matchesSearch = p.nama_inovasi?.toLowerCase().includes(searchQuery.value.toLowerCase())
    if (activeTab.value === 'Semua') return matchesSearch
    const statusMap = { 'Draft': 'draft', 'Verifikasi': 'pending', 'Selesai': 'approved' }
    return matchesSearch && p.status_kurasi === statusMap[activeTab.value]
  })
})

function getStatusIcon(status) {
  const map = { pending: Clock, approved: CheckCircle2, rejected: AlertCircle, draft: FileText }
  return map[status] || FileText
}

function getStatusBadgeClass(status) {
  const map = {
    pending: 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/30',
    approved: 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/30',
    rejected: 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/30',
    draft: 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700',
  }
  return map[status] || 'bg-slate-50 text-slate-600 border border-slate-200'
}

function getStatusBgLight(status) {
  const map = {
    pending: 'bg-amber-100 dark:bg-amber-950/30',
    approved: 'bg-emerald-100 dark:bg-emerald-950/30',
    rejected: 'bg-rose-100 dark:bg-rose-950/30',
    draft: 'bg-slate-100 dark:bg-slate-800',
  }
  return map[status] || 'bg-slate-100 dark:bg-slate-800'
}

function getStatusTextColor(status) {
  const map = {
    pending: 'text-amber-600 dark:text-amber-400',
    approved: 'text-emerald-600 dark:text-emerald-400',
    rejected: 'text-rose-600 dark:text-rose-400',
    draft: 'text-slate-500 dark:text-slate-400',
  }
  return map[status] || 'text-slate-500'
}

function getStatusDotColor(status) {
  const map = {
    pending: 'bg-amber-500 animate-pulse',
    approved: 'bg-emerald-500',
    rejected: 'bg-rose-500',
    draft: 'bg-slate-400',
  }
  return map[status] || 'bg-slate-400'
}

function getStatusLabel(status) {
  const map = { pending: 'Menunggu Verifikasi', approved: 'Disetujui', rejected: 'Ditolak', draft: 'Draft' }
  return map[status] || status
}

async function loadMyProducts() {
  try {
    const res = await api.get('/inisiator/products')
    myProducts.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

function goToDetail(id) {
  router.push(`/inisiator/products/${id}`)
}

function editProduct(id) {
  router.push(`/inisiator/pengajuan?id=${id}`)
}

onMounted(loadMyProducts)
</script>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.35s ease;
}
.slide-fade-leave-active {
  transition: all 0.25s ease;
}
.slide-fade-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}

.fade-up-enter-active {
  transition: all 0.3s ease;
}
.fade-up-leave-active {
  transition: all 0.2s ease;
}
.fade-up-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.fade-up-leave-to {
  opacity: 0;
}
</style>
