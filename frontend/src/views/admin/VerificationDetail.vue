<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />

    <main class="flex-1 p-6 sm:p-8 overflow-y-auto max-w-7xl mx-auto w-full">

      <!-- Loading State -->
      <div v-if="loading" class="space-y-6">
        <div class="skeleton h-8 w-48 rounded-xl"></div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-5">
            <div class="skeleton h-64 rounded-2xl"></div>
            <div class="skeleton h-48 rounded-2xl"></div>
          </div>
          <div class="skeleton h-80 rounded-2xl"></div>
        </div>
      </div>

      <!-- Not Found -->
      <div v-else-if="!product" class="flex flex-col items-center justify-center py-24 gap-4">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
          <FileX class="w-8 h-8 text-slate-300 dark:text-slate-600" />
        </div>
        <p class="text-sm font-semibold text-slate-500">Produk tidak ditemukan.</p>
        <button
          @click="$router.push('/admin/verifikasi')"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-blue-700 transition-all cursor-pointer"
        >
          <ArrowLeft class="w-4 h-4" /> Kembali
        </button>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Page Header -->
        <div class="pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
              <button
                @click="$router.push('/admin/verifikasi')"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer"
              >
                <ArrowLeft class="w-4 h-4" /> Kembali
              </button>
              <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Detail Verifikasi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                  Tinjau dan verifikasi ajuan inovasi ini.
                </p>
              </div>
            </div>
            <!-- Current Status Badge -->
            <span
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-bold self-start"
              :class="getStatusBadgeClass(product.status_kurasi)"
            >
              <span class="w-2 h-2 rounded-full" :class="getStatusDotColor(product.status_kurasi)"></span>
              {{ getStatusLabel(product.status_kurasi) }}
            </span>
          </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <!-- Left: Main Content -->
          <div class="lg:col-span-2 space-y-5">

            <!-- Product Info Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
              <!-- Header with colored accent -->
              <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/50 flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                  :class="getStatusBgLight(product.status_kurasi)">
                  <FileCheck class="w-6 h-6" :class="getStatusTextColor(product.status_kurasi)" />
                </div>
                <div>
                  <h2 class="text-xl font-extrabold text-slate-900 dark:text-white leading-snug">
                    {{ product.nama_inovasi }}
                  </h2>
                  <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                    Tahun {{ product.tahun_inovasi }} · {{ product.opd?.nama_opd || 'Umum' }}
                    <span v-if="product.is_digital" class="ml-2 px-2 py-0.5 text-[10px] font-bold rounded bg-primary/10 text-primary">Digital</span>
                  </p>
                </div>
              </div>

              <!-- Image Gallery -->
              <div class="p-6">
                <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Foto Dokumentasi</h3>
                <div v-if="productImages.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  <div
                    v-for="(img, idx) in productImages"
                    :key="idx"
                    class="relative group overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 aspect-video"
                  >
                    <img
                      :src="getImageUrl(img.isi_konten)"
                      :alt="'Foto produk ' + (idx + 1)"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                      @error="handleImgError($event)"
                    />
                  </div>
                </div>
                <div v-else class="flex flex-col items-center justify-center h-32 rounded-xl bg-slate-50 dark:bg-slate-950/40 border-2 border-dashed border-slate-200 dark:border-slate-800 gap-2">
                  <ImageOff class="w-8 h-8 text-slate-300 dark:text-slate-600" />
                  <p class="text-xs font-medium text-slate-400">Tidak ada foto dokumentasi</p>
                </div>
              </div>

              <!-- Description -->
              <div class="px-6 pb-6">
                <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Deskripsi Inovasi</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                  {{ product.deskripsi || 'Tidak ada deskripsi.' }}
                </p>
              </div>
            </div>

            <!-- Rejection Note (if rejected) -->
            <div v-if="product.alasan_penolakan" class="bg-rose-50 dark:bg-rose-950/20 rounded-2xl border border-rose-200 dark:border-rose-900/40 p-5">
              <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center flex-shrink-0">
                  <XCircle class="w-5 h-5 text-rose-600" />
                </div>
                <div>
                  <h4 class="text-sm font-bold text-rose-700 dark:text-rose-400 mb-1">Alasan Penolakan</h4>
                  <p class="text-sm text-rose-600 dark:text-rose-400/80 italic leading-relaxed">
                    "{{ product.alasan_penolakan }}"
                  </p>
                </div>
              </div>
            </div>

          </div>

          <!-- Right: Sidebar -->
          <div class="space-y-5">

            <!-- Admin Actions -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm p-5">
              <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                Tindakan Verifikasi
              </h3>

              <div class="space-y-3">
                <!-- Approve Button -->
                <button
                  @click="handleVerify('approved')"
                  :disabled="submitting || product.status_kurasi === 'approved'"
                  class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  :class="product.status_kurasi === 'approved'
                    ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40'
                    : 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm shadow-emerald-500/20'"
                >
                  <CheckCircle2 class="w-4 h-4" />
                  {{ product.status_kurasi === 'approved' ? 'Sudah Disetujui' : 'Setujui Inovasi' }}
                </button>

                <!-- Reject Button -->
                <button
                  @click="openRejectModal"
                  :disabled="submitting || product.status_kurasi === 'rejected'"
                  class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold border transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  :class="product.status_kurasi === 'rejected'
                    ? 'bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-900/40'
                    : 'border-rose-300 dark:border-rose-800 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/20'"
                >
                  <XCircle class="w-4 h-4" />
                  {{ product.status_kurasi === 'rejected' ? 'Sudah Ditolak' : 'Tolak Inovasi' }}
                </button>
              </div>
            </div>

            <!-- Info Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm p-5">
              <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                Informasi Administratif
              </h3>

              <div class="space-y-4">
                <div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahapan</p>
                  <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    {{ product.tahapan_inovasi?.nama_tahapan || '-' }}
                  </p>
                </div>
                <div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Inisiator</p>
                  <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    {{ product.inisiator_profile?.nama_inisiator || '-' }}
                  </p>
                </div>
                <div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kontak Inisiator</p>
                  <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <a v-if="product.inisiator_profile?.kontak" :href="'tel:' + product.inisiator_profile.kontak" class="hover:text-primary transition-colors">{{ product.inisiator_profile.kontak }}</a>
                    <span v-else>-</span>
                  </p>
                </div>
                <div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">OPD</p>
                  <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    {{ product.opd?.nama_opd || '-' }}
                  </p>
                </div>
                <div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Review</p>
                  <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    {{ product.tanggal_review ? formatDate(product.tanggal_review) : '-' }}
                  </p>
                </div>
                <div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis</p>
                  <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-lg text-primary bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/30">
                    {{ product.is_digital ? 'Digital' : 'Non-Digital' }}
                  </span>
                </div>
                <div v-if="product.link_marketplace">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Link Referensi</p>
                  <a
                    :href="product.link_marketplace"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline"
                  >
                    <ExternalLink class="w-3 h-3" />
                    Lihat Link
                  </a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>

    <!-- Reject Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showRejectModal = false">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
          <div class="relative bg-white dark:bg-slate-900 rounded-2xl w-full max-w-[520px] shadow-2xl overflow-hidden border border-slate-200/50 dark:border-slate-800/50 animate-scale-in">
            <!-- Modal Header -->
            <div class="p-6 pb-4 border-b border-slate-100 dark:border-slate-800/50">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center">
                    <XCircle class="w-5 h-5 text-rose-600" />
                  </div>
                  <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tolak Inovasi</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Berikan alasan penolakan yang jelas</p>
                  </div>
                </div>
                <button
                  @click="showRejectModal = false"
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer"
                >
                  <X class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
              <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-3">Pilih alasan cepat:</p>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="reason in presetReasons"
                    :key="reason"
                    @click="rejectionReason = reason"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all cursor-pointer"
                    :class="rejectionReason === reason
                      ? 'bg-rose-100 dark:bg-rose-950/40 border-rose-400 text-rose-700 dark:text-rose-400'
                      : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 hover:border-rose-300 dark:hover:border-rose-800 hover:text-rose-600'"
                  >
                    {{ reason }}
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                  Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea
                  v-model="rejectionReason"
                  rows="4"
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-400 transition-all resize-none"
                  placeholder="Tulis alasan penolakan secara lengkap..."
                ></textarea>
                <p v-if="rejectionError" class="text-xs text-rose-500 font-medium mt-1">{{ rejectionError }}</p>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800/50 flex gap-3 justify-end">
              <button
                @click="showRejectModal = false"
                class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer"
              >
                Batal
              </button>
              <button
                @click="submitRejection"
                :disabled="submitting"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all cursor-pointer disabled:opacity-50"
              >
                <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
                <XCircle v-else class="w-4 h-4" />
                {{ submitting ? 'Memproses...' : 'Konfirmasi Tolak' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToastStore } from '../../stores/toast'
import api from '../../services/api'
import {
  ArrowLeft, FileCheck, FileX, CheckCircle2, XCircle, X,
  ImageOff, ExternalLink, Loader2
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const toastStore = useToastStore()

const product = ref(null)
const loading = ref(true)
const submitting = ref(false)
const showRejectModal = ref(false)
const rejectionReason = ref('')
const rejectionError = ref('')

const presetReasons = [
  'Deskripsi kurang lengkap',
  'Gambar tidak sesuai',
  'Tidak memenuhi ketentuan',
  'Kategori salah',
  'Informasi kurang jelas',
  'Duplikat produk',
]

const productImages = computed(() => {
  if (!product.value?.media_inovasi) return []
  return product.value.media_inovasi.filter(
    m => m.jenis_media === 'foto' || m.jenis_media === 'image' || m.jenis_media === 'foto_produk'
  )
})

function getImageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `${import.meta.env.VITE_API_BASE_URL?.replace('/api', '') || 'http://localhost:8000'}${path}`
}

function handleImgError(e) {
  e.target.style.display = 'none'
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
}

function getStatusBadgeClass(status) {
  const map = {
    pending: 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/30',
    approved: 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/30',
    rejected: 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/30',
  }
  return map[status] || 'bg-slate-50 text-slate-600 border border-slate-200'
}

function getStatusBgLight(status) {
  const map = {
    pending: 'bg-amber-100 dark:bg-amber-950/30',
    approved: 'bg-emerald-100 dark:bg-emerald-950/30',
    rejected: 'bg-rose-100 dark:bg-rose-950/30',
  }
  return map[status] || 'bg-slate-100 dark:bg-slate-800'
}

function getStatusTextColor(status) {
  const map = {
    pending: 'text-amber-600 dark:text-amber-400',
    approved: 'text-emerald-600 dark:text-emerald-400',
    rejected: 'text-rose-600 dark:text-rose-400',
  }
  return map[status] || 'text-slate-500'
}

function getStatusDotColor(status) {
  const map = {
    pending: 'bg-amber-500 animate-pulse',
    approved: 'bg-emerald-500',
    rejected: 'bg-rose-500',
  }
  return map[status] || 'bg-slate-400'
}

function getStatusLabel(status) {
  const map = { pending: 'Menunggu Review', approved: 'Disetujui', rejected: 'Ditolak' }
  return map[status] || status
}

async function loadProduct() {
  loading.value = true
  try {
    const res = await api.get(`/admin/products/${route.params.id}`)
    product.value = res.data
  } catch (e) {
    console.error('Failed to load product', e)
    toastStore.show('Gagal memuat detail inovasi.', 'error')
  } finally {
    loading.value = false
  }
}

async function handleVerify(status) {
  if (!confirm('Apakah Anda yakin ingin menyetujui inovasi ini?')) return
  submitting.value = true
  try {
    await api.put(`/admin/products/${route.params.id}/verify`, { status_kurasi: status })
    toastStore.show('Inovasi berhasil disetujui!', 'success')
    await loadProduct()
  } catch (e) {
    toastStore.show(e.response?.data?.message || 'Gagal mengubah status.', 'error')
  } finally {
    submitting.value = false
  }
}

function openRejectModal() {
  rejectionReason.value = ''
  rejectionError.value = ''
  showRejectModal.value = true
}

async function submitRejection() {
  if (!rejectionReason.value.trim()) {
    rejectionError.value = 'Alasan penolakan wajib diisi.'
    return
  }
  submitting.value = true
  try {
    await api.put(`/admin/products/${route.params.id}/verify`, {
      status_kurasi: 'rejected',
      alasan_penolakan: rejectionReason.value.trim()
    })
    showRejectModal.value = false
    toastStore.show('Inovasi berhasil ditolak.', 'success')
    await loadProduct()
  } catch (e) {
    toastStore.show(e.response?.data?.message || 'Gagal menolak produk.', 'error')
  } finally {
    submitting.value = false
  }
}

onMounted(loadProduct)
</script>

<style scoped>
@keyframes scale-in {
  from { transform: scale(0.92); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.animate-scale-in {
  animation: scale-in 0.2s ease-out;
}
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>
