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

              <!-- Product Images -->
              <div class="product-images mb-4">
                <div v-if="productImages.length > 0" class="image-gallery">
                  <img
                    v-for="(img, idx) in productImages"
                    :key="idx"
                    :src="getImageUrl(img.isi_konten)"
                    :alt="'Foto produk ' + (idx + 1)"
                    class="gallery-img"
                    @error="handleImgError($event)"
                  />
                </div>
                <div v-else class="no-image-placeholder">
                  <i class='bx bx-image-alt'></i>
                  <span>Tidak ada foto produk</span>
                </div>
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
                <div class="info-item" v-if="product.alasan_penolakan">
                  <label>Alasan Penolakan</label>
                  <span class="rejection-reason-text">{{ product.alasan_penolakan }}</span>
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
                <div class="info-item">
                  <label>Tanggal Review</label>
                  <span>{{ product.tanggal_review ? formatDate(product.tanggal_review) : '-' }}</span>
                </div>
              </div>

              <div class="action-buttons mt-4">
                <button
                  class="btn btn-primary w-full mb-2"
                  @click="handleVerify('approved')"
                  :disabled="submitting || product.status_kurasi === 'approved'"
                >
                  <i class='bx bx-check'></i> SETUJUI
                </button>
                <button
                  class="btn-reject w-full"
                  @click="openRejectModal"
                  :disabled="submitting || product.status_kurasi === 'rejected'"
                >
                  <i class='bx bx-x'></i> TOLAK
                </button>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </main>

    <!-- Rejection Modal -->
    <Teleport to="body">
      <div v-if="showRejectModal" class="modal-overlay" @click.self="showRejectModal = false">
        <div class="modal-box">
          <div class="modal-header">
            <h3>Alasan Penolakan</h3>
            <button class="modal-close" @click="showRejectModal = false">×</button>
          </div>
          <div class="modal-body">
            <p class="modal-subtitle">Pilih atau ketik alasan penolakan untuk inovasi ini:</p>

            <div class="preset-reasons">
              <button
                v-for="reason in presetReasons"
                :key="reason"
                class="preset-btn"
                :class="{ selected: rejectionReason === reason }"
                @click="rejectionReason = reason"
              >
                {{ reason }}
              </button>
            </div>

            <div class="reason-input-group mt-3">
              <label>Alasan Penolakan *</label>
              <textarea
                v-model="rejectionReason"
                rows="4"
                class="form-control"
                placeholder="Tulis alasan penolakan secara lengkap..."
              ></textarea>
              <span v-if="rejectionError" class="error-text">{{ rejectionError }}</span>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" @click="showRejectModal = false">Batal</button>
            <button class="btn-reject-confirm" @click="submitRejection" :disabled="submitting">
              <i class='bx bx-x-circle'></i> Konfirmasi Tolak
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const router = useRouter()
const product = ref(null)
const loading = ref(true)
const submitting = ref(false)
const showRejectModal = ref(false)
const rejectionReason = ref('')
const rejectionError = ref('')

const presetReasons = [
  'Deskripsi produk kurang lengkap',
  'Gambar produk tidak sesuai',
  'Produk tidak memenuhi ketentuan platform',
  'Kategori produk salah',
  'Informasi masih kurang jelas',
  'Produk duplikat',
]

const productImages = computed(() => {
  if (!product.value?.media_inovasi) return []
  return product.value.media_inovasi.filter(m => m.jenis_media === 'foto' || m.jenis_media === 'image' || m.jenis_media === 'foto_produk')
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
  if (!confirm(`Apakah Anda yakin ingin menyetujui inovasi ini?`)) return
  submitting.value = true
  try {
    await api.put(`/admin/products/${route.params.id}/verify`, { status_kurasi: status })
    alert('Inovasi berhasil disetujui.')
    await loadProduct()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal mengubah status.')
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
    await loadProduct()
    alert('Inovasi berhasil ditolak.')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menolak produk.')
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

.rejection-reason-text {
  color: #dc2626;
  font-size: 0.875rem !important;
  font-style: italic;
}

/* Image Gallery */
.image-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 0.75rem;
}

.gallery-img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.no-image-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 120px;
  background: var(--bg-light, #f8fafc);
  border-radius: 8px;
  color: var(--text-muted);
  gap: 0.5rem;
  font-size: 0.875rem;
}

.no-image-placeholder i {
  font-size: 2rem;
  opacity: 0.4;
}

/* Action Buttons */
.btn-reject {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  width: 100%;
  padding: 0.625rem 1.25rem;
  background: transparent;
  color: #dc2626;
  border: 1.5px solid #dc2626;
  border-radius: var(--border-radius, 8px);
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-reject:hover:not(:disabled) {
  background: #fef2f2;
}

.btn-reject:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.modal-box {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  overflow: hidden;
  animation: modalIn 0.2s ease;
}

@keyframes modalIn {
  from { transform: scale(0.9); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.modal-header h3 {
  font-size: 1.125rem;
  font-weight: 700;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  line-height: 1;
  color: var(--text-muted);
}

.modal-body {
  padding: 1.5rem;
}

.modal-subtitle {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 1rem;
}

.preset-reasons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.preset-btn {
  padding: 0.4rem 0.9rem;
  border-radius: 99px;
  border: 1px solid var(--border-color, #e5e7eb);
  background: #f9fafb;
  font-size: 0.8125rem;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
}

.preset-btn:hover {
  border-color: #dc2626;
  color: #dc2626;
}

.preset-btn.selected {
  background: #fef2f2;
  border-color: #dc2626;
  color: #dc2626;
}

.reason-input-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.error-text {
  color: #dc2626;
  font-size: 0.8125rem;
  margin-top: 0.25rem;
  display: block;
}

.modal-footer {
  display: flex;
  gap: 0.75rem;
  padding: 1.25rem 1.5rem;
  border-top: 1px solid var(--border-color, #e5e7eb);
  justify-content: flex-end;
}

.btn-reject-confirm {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.625rem 1.25rem;
  background: #dc2626;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-reject-confirm:hover:not(:disabled) {
  background: #b91c1c;
}

.btn-reject-confirm:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
