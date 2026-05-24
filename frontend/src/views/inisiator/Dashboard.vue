<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4">
        <h1 class="page-title">Dashboard Inisiator</h1>
        <p class="text-muted">Ajukan dan pantau status produk inovasi Anda.</p>
      </div>

      <!-- Stats -->
      <div class="stats-row mb-4">
        <div class="stat-card">
          <div class="stat-value">{{ myProducts.length }}</div>
          <div class="stat-label">Total Ajuan</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ myProducts.filter(p => p.status_kurasi === 'approved').length }}</div>
          <div class="stat-label">Disetujui</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ myProducts.filter(p => p.status_kurasi === 'pending').length }}</div>
          <div class="stat-label">Menunggu</div>
        </div>
      </div>

      <!-- Tabs & Search -->
      <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="tabs">
            <button v-for="tab in ['Semua', 'Draft', 'Verifikasi', 'Selesai']" :key="tab"
              :class="['tab-btn', { active: activeTab === tab }]"
              @click="activeTab = tab">
              {{ tab }}
            </button>
          </div>
          <input v-model="searchQuery" class="form-control" placeholder="Cari inovasi..." style="max-width: 250px;" />
        </div>

        <div v-if="loading" class="text-center py-5">Memuat data...</div>

        <div v-else class="innovation-grid">
          <div v-for="product in filteredProducts" :key="product.id" class="innovation-item card">
            <div class="item-header">
              <h3 class="item-title">{{ product.nama_inovasi }}</h3>
              <span :class="['badge', getStatusClass(product.status_kurasi)]">{{ getStatusLabel(product.status_kurasi) }}</span>
            </div>
            <p class="item-desc text-muted">{{ product.deskripsi || 'Tidak ada deskripsi.' }}</p>
            <div class="item-footer">
              <span class="text-light">{{ product.tahun_inovasi }}</span>
              <router-link :to="`/inisiator/pengajuan?id=${product.id}`" class="btn btn-outline btn-sm">Edit</router-link>
            </div>
          </div>
          <div v-if="filteredProducts.length === 0" class="text-center py-4 text-muted" style="grid-column: 1 / -1;">
            Tidak ada inovasi ditemukan.
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, onMounted, computed } from 'vue'
import api from '../../services/api'

const myProducts = ref([])
const loading = ref(true)
const searchQuery = ref('')
const activeTab = ref('Semua')

const filteredProducts = computed(() => {
  return myProducts.value.filter(p => {
    const matchesSearch = p.nama_inovasi.toLowerCase().includes(searchQuery.value.toLowerCase())
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

function getStatusLabel(status) {
  switch (status) {
    case 'pending': return 'Verifikasi'
    case 'approved': return 'Selesai'
    case 'rejected': return 'Ditolak'
    case 'draft': return 'Draft'
    default: return status
  }
}

function getStatusClass(status) {
  switch (status) {
    case 'pending': return 'badge-pending'
    case 'approved': return 'badge-success'
    case 'rejected': return 'badge-danger'
    case 'draft': return 'badge-secondary'
    default: return ''
  }
}

async function loadMyProducts() {
  loading.value = true
  try {
    const res = await api.get('/inisiator/products')
    myProducts.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(loadMyProducts)
</script>

<style scoped>
.stats-row {
  display: flex;
  gap: 1rem;
}

.stat-card {
  flex: 1;
  background: var(--card-bg, #fff);
  border: 1px solid var(--border-color, #e2e8f0);
  border-radius: 8px;
  padding: 1.25rem;
  text-align: center;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 800;
  color: var(--primary, #2563eb);
}

.stat-label {
  font-size: 0.85rem;
  color: var(--text-light, #64748b);
  margin-top: 0.25rem;
}

.tabs {
  display: flex;
  gap: 0.5rem;
}

.tab-btn {
  background: none;
  border: 1px solid var(--border-color, #e2e8f0);
  padding: 0.4rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.85rem;
  font-weight: 500;
  transition: all 0.2s;
}

.tab-btn.active {
  background: var(--primary, #2563eb);
  color: #fff;
  border-color: var(--primary, #2563eb);
}

.innovation-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.innovation-item {
  padding: 1.25rem;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.item-title {
  font-size: 1rem;
  font-weight: 700;
  margin: 0;
}

.item-desc {
  font-size: 0.85rem;
  margin-bottom: 1rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
