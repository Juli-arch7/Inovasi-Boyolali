<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="header-with-action mb-4">
        <div class="page-header">
          <h1 class="page-title">Inovasi Saya</h1>
          <p class="text-muted">Kelola dan pantau semua inovasi yang telah Anda ajukan.</p>
        </div>
        <button class="btn btn-primary" @click="$router.push('/inisiator/pengajuan')">
          <i class='bx bx-plus'></i> Ajukan Inovasi Baru
        </button>
      </div>

      <!-- Rejection Notifications -->
      <div v-if="rejectedProducts.length > 0" class="notifications-section mb-4">
        <div
          v-for="product in rejectedProducts"
          :key="'notif-' + product.id"
          class="rejection-card"
        >
          <div class="rejection-icon">
            <i class='bx bx-x-circle'></i>
          </div>
          <div class="rejection-content">
            <div class="rejection-header">
              <h4>Pengajuan Produk Inovasi Anda Ditolak</h4>
              <span class="rejection-date" v-if="product.tanggal_review">
                {{ formatDate(product.tanggal_review) }}
              </span>
            </div>
            <p class="rejection-name">🔖 <strong>{{ product.nama_inovasi }}</strong></p>
            <div class="rejection-reason-box">
              <span class="reason-label">Alasan Penolakan:</span>
              <p class="reason-text">{{ product.alasan_penolakan || 'Tidak ada alasan yang diberikan.' }}</p>
            </div>
            <div class="rejection-actions">
              <button class="btn-resubmit" @click="editProduct(product.id)">
                <i class='bx bx-refresh'></i> Ajukan Ulang
              </button>
            </div>
          </div>
          <button class="rejection-dismiss" @click="dismissNotification(product.id)" title="Tutup">×</button>
        </div>
      </div>

      <div class="tabs mb-4">
        <button v-for="tab in tabs" :key="tab" :class="['tab-item', { active: activeTab === tab }]" @click="activeTab = tab">
          {{ tab }}
        </button>
      </div>

      <div class="search-bar mb-4">
        <div class="input-with-icon">
          <i class='bx bx-search icon'></i>
          <input type="text" class="form-control" v-model="searchQuery" placeholder="Cari Inovasi...">
        </div>
      </div>

      <div v-if="loading" class="text-center py-5">Memuat data...</div>

      <div v-else class="innovation-grid">
        <div v-for="product in filteredProducts" :key="product.id" class="innovation-item card">
          <div class="item-header">
            <h3 class="item-title">{{ product.nama_inovasi }}</h3>
            <span :class="['badge', `badge-${product.status_kurasi}`]">{{ product.status_kurasi }}</span>
          </div>
          <p class="item-description">{{ product.deskripsi || 'Tidak ada deskripsi.' }}</p>
          <div class="item-footer">
            <span class="item-meta">Tahun: {{ product.tahun_inovasi }}</span>
            <div class="item-actions">
              <button class="btn btn-outline btn-sm" @click="goToDetail(product.id)">Detail</button>
              <button class="btn btn-outline btn-sm" @click="editProduct(product.id)">Edit</button>
            </div>
          </div>
        </div>
        <div v-if="filteredProducts.length === 0" class="text-center py-5 w-full">
          Belum ada inovasi yang sesuai dengan kriteria.
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

const router = useRouter()
const myProducts = ref([])
const loading = ref(true)
const activeTab = ref('Semua')
const tabs = ['Semua', 'Draft', 'Verifikasi', 'Selesai']
const searchQuery = ref('')
const dismissedIds = ref(new Set())

const rejectedProducts = computed(() => {
  return myProducts.value.filter(
    p => p.status_kurasi === 'rejected' && !dismissedIds.value.has(p.id)
  )
})

function dismissNotification(id) {
  dismissedIds.value = new Set([...dismissedIds.value, id])
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: '2-digit', month: 'long', year: 'numeric'
  })
}

const filteredProducts = computed(() => {
  return myProducts.value.filter(p => {
    const matchesSearch = p.nama_inovasi?.toLowerCase().includes(searchQuery.value.toLowerCase())
    if (activeTab.value === 'Semua') return matchesSearch

    let statusMatch = false
    if (activeTab.value === 'Draft') {
      statusMatch = p.status_kurasi === 'draft'
    } else if (activeTab.value === 'Verifikasi') {
      statusMatch = p.status_kurasi === 'pending'
    } else if (activeTab.value === 'Selesai') {
      statusMatch = p.status_kurasi === 'approved'
    }
    return matchesSearch && statusMatch
  })
})

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
.header-with-action {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

/* Rejection Notifications */
.notifications-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.rejection-card {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  background: linear-gradient(135deg, #fff5f5, #fff1f1);
  border: 1.5px solid #fca5a5;
  border-left: 5px solid #dc2626;
  border-radius: 12px;
  padding: 1.25rem 1.5rem;
  position: relative;
  animation: slideIn 0.35s ease;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.rejection-icon {
  font-size: 2rem;
  color: #dc2626;
  flex-shrink: 0;
  line-height: 1;
  margin-top: 0.1rem;
}

.rejection-content {
  flex: 1;
}

.rejection-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.rejection-header h4 {
  font-size: 1rem;
  font-weight: 700;
  color: #991b1b;
  margin: 0;
}

.rejection-date {
  font-size: 0.8125rem;
  color: #6b7280;
  font-weight: 500;
}

.rejection-name {
  font-size: 0.9rem;
  margin-bottom: 0.75rem;
  color: #374151;
}

.rejection-reason-box {
  background: rgba(220, 38, 38, 0.06);
  border-radius: 8px;
  padding: 0.75rem 1rem;
  margin-bottom: 1rem;
}

.reason-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: #991b1b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: block;
  margin-bottom: 0.25rem;
}

.reason-text {
  font-size: 0.9rem;
  color: #374151;
  margin: 0;
  line-height: 1.5;
}

.rejection-actions {
  display: flex;
  gap: 0.75rem;
}

.btn-resubmit {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1.25rem;
  background: #dc2626;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-resubmit:hover {
  background: #b91c1c;
}

.rejection-dismiss {
  position: absolute;
  top: 0.75rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #9ca3af;
  cursor: pointer;
  line-height: 1;
}

.rejection-dismiss:hover {
  color: #374151;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 1rem;
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 0.5rem;
}

.tab-item {
  background: none;
  border: none;
  padding: 0.5rem 1rem;
  font-weight: 600;
  color: var(--text-muted);
  cursor: pointer;
  position: relative;
}

.tab-item.active {
  color: var(--primary);
}

.tab-item.active::after {
  content: '';
  position: absolute;
  bottom: -0.5rem;
  left: 0;
  width: 100%;
  height: 2px;
  background: var(--primary);
}

.search-bar {
  max-width: 400px;
}

.input-with-icon {
  position: relative;
}

.input-with-icon .icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-light);
}

.input-with-icon .form-control {
  padding-left: 40px;
}

.innovation-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.innovation-item {
  padding: 1.5rem;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.item-title {
  font-size: 1.125rem;
  font-weight: 700;
  margin: 0;
}

.item-description {
  color: var(--text-muted);
  font-size: 0.875rem;
  margin-bottom: 1.5rem;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid var(--border-color);
  padding-top: 1rem;
}

.item-meta {
  font-size: 0.8125rem;
  color: var(--text-light);
  font-weight: 500;
}

.item-actions {
  display: flex;
  gap: 0.5rem;
}
</style>
