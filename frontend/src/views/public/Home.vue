<template>
  <div class="home-page">
    <section class="home-hero">
      <div class="container">
        <h1>TEMUKAN <span>INOVASI</span></h1>
        <div class="search-container">
          <input type="text" v-model="searchQuery" placeholder="Cari Inovasi..." class="search-input">
          <button class="btn btn-primary search-btn">CARI</button>
        </div>
      </div>
    </section>

    <div class="container main-layout">
      <aside class="filter-sidebar">
        <div class="filter-group">
          <h3>Filter</h3>
          <div class="filter-item">
            <label>Kategori</label>
            <select v-model="filters.category" class="form-control">
              <option value="">Semua Kategori</option>
              <option value="digital">Digital</option>
              <option value="non-digital">Non Digital</option>
            </select>
          </div>
          <div class="filter-item">
            <label>Bentuk Inovasi</label>
            <select v-model="filters.bentuk" class="form-control">
              <option value="">Semua Bentuk</option>
            </select>
          </div>
          <div class="filter-item">
            <label>Kematangan</label>
            <select v-model="filters.kematangan" class="form-control">
              <option value="">Semua</option>
            </select>
          </div>
          <div class="filter-item">
            <label>Waktu Uji Coba</label>
            <select v-model="filters.waktu" class="form-control">
              <option value="">Semua</option>
            </select>
          </div>
          <div class="filter-item">
            <label>Urutkan</label>
            <select v-model="filters.sort" class="form-control">
              <option value="newest">Terbaru</option>
              <option value="oldest">Terlama</option>
            </select>
          </div>
          <button class="btn btn-primary w-full mt-4" @click="applyFilters">Terapkan Filter</button>
        </div>
      </aside>

      <main class="innovation-list">
        <div v-if="loading" class="text-center py-5">
          Memuat data...
        </div>

        <div v-else-if="filteredProducts.length === 0" class="text-center py-5">
          Belum ada produk inovasi yang ditampilkan.
        </div>

        <div v-else class="grid grid-cols-3">
          <div class="innovation-card card" v-for="product in filteredProducts" :key="product.id" @click="goToDetail(product.id)">
            <div class="card-image">
              <img :src="product.thumbnail || 'https://via.placeholder.com/300x200'" alt="Innovation Thumbnail">
            </div>
            <div class="card-body">
              <div class="card-meta">
                <span class="badge-custom">{{ product.tahun_inovasi }}</span>
              </div>
              <h3 class="card-title">{{ product.nama_inovasi }}</h3>
              <p class="card-description">{{ product.deskripsi || 'Tidak ada deskripsi.' }}</p>
              <div class="card-footer">
                <span class="view-count">👁️ 100</span>
                <span class="date">📅 12 Jan 2024</span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const products = ref([])
const loading = ref(true)
const searchQuery = ref('')
const filters = ref({
  category: '',
  bentuk: '',
  kematangan: '',
  waktu: '',
  sort: 'newest'
})

const filteredProducts = computed(() => {
  return products.value.filter(p => 
    p.nama_inovasi.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

onMounted(async () => {
  try {
    const res = await api.get('/public/products')
    products.value = res.data
  } catch (e) {
    console.error('Failed to load products', e)
  } finally {
    loading.value = false
  }
})

function applyFilters() {
  // Logic to apply filters
}

function goToDetail(id) {
  router.push(`/products/${id}`)
}
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.home-hero {
  background-color: var(--bg-white);
  padding: 4rem 0;
  text-align: center;
  border-bottom: 1px solid var(--border-color);
}

.home-hero h1 {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 2rem;
}

.home-hero h1 span {
  color: var(--primary);
}

.search-container {
  max-width: 600px;
  margin: 0 auto;
  display: flex;
  gap: 0.5rem;
  background: var(--bg-white);
  padding: 0.5rem;
  border-radius: 99px;
  box-shadow: var(--shadow);
  border: 1px solid var(--border-color);
}

.search-input {
  flex: 1;
  border: none;
  padding: 0.5rem 1.5rem;
  outline: none;
  font-size: 1rem;
}

.search-btn {
  border-radius: 99px;
  padding: 0.5rem 2rem;
}

.main-layout {
  display: flex;
  gap: 2rem;
  padding-top: 2rem;
  padding-bottom: 4rem;
}

.filter-sidebar {
  width: 280px;
  flex-shrink: 0;
}

.filter-group {
  background: var(--bg-white);
  padding: 1.5rem;
  border-radius: var(--border-radius);
  border: 1px solid var(--border-color);
  position: sticky;
  top: 90px;
}

.filter-group h3 {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
}

.filter-item {
  margin-bottom: 1.25rem;
}

.filter-item label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--text-muted);
}

.innovation-list {
  flex: 1;
}

.innovation-card {
  cursor: pointer;
  padding: 0;
  overflow: hidden;
  transition: var(--transition);
}

.innovation-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.card-image img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.card-body {
  padding: 1.25rem;
}

.card-title {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  line-height: 1.4;
}

.card-description {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 1.25rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: var(--text-light);
  border-top: 1px solid var(--border-color);
  padding-top: 1rem;
}

.badge-custom {
  background: var(--primary-light);
  color: var(--primary);
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 700;
}
</style>
