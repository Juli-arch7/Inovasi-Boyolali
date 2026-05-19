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
          <span class="icon">+</span> Ajukan Inovasi Baru
        </button>
      </div>

      <div class="tabs mb-4">
        <button v-for="tab in tabs" :key="tab" :class="['tab-item', { active: activeTab === tab }]" @click="activeTab = tab">
          {{ tab }}
        </button>
      </div>

      <div class="search-bar mb-4">
        <div class="input-with-icon">
          <span class="icon">🔍</span>
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

const filteredProducts = computed(() => {
  return myProducts.value.filter(p => {
    const matchesSearch = p.nama_inovasi.toLowerCase().includes(searchQuery.value.toLowerCase())
    if (activeTab.value === 'Semua') return matchesSearch
    return matchesSearch && p.status_kurasi === activeTab.value.toLowerCase()
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
