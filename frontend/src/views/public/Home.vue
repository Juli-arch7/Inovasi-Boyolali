<template>
  <div class="home-page">
    <section class="home-hero">
      <div class="container">
        <h1>TEMUKAN <span>INOVASI</span></h1>
        <div class="search-container">
          <input type="text" v-model="searchQuery" placeholder="Cari Inovasi..." class="search-input">
          <button class="btn btn-primary search-btn">CARI</button>
        </div>
        <div class="category-pills">
          <button 
            class="pill-btn" 
            :class="{ active: filters.category === '' }"
            @click="filters.category = ''"
          >Semua</button>
          <button 
            class="pill-btn" 
            :class="{ active: filters.category === 'Pelayanan Publik' }"
            @click="filters.category = 'Pelayanan Publik'"
          >Pelayanan Publik</button>
          <button 
            class="pill-btn" 
            :class="{ active: filters.category === 'Tata Kelola Pemerintahan' }"
            @click="filters.category = 'Tata Kelola Pemerintahan'"
          >Tata Kelola Pemerintahan</button>
          <button 
            class="pill-btn" 
            :class="{ active: filters.category === 'Inovasi Daerah Lainnya' }"
            @click="filters.category = 'Inovasi Daerah Lainnya'"
          >Inovasi Daerah Lainnya</button>
        </div>
      </div>
    </section>

    <div class="container main-layout">
      <aside class="filter-sidebar">
        <div class="filter-group">
          <div class="filter-header">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4 6H20M7 12H17M10 18H14" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3>Filter</h3>
          </div>
          <hr class="filter-divider" />
          
          <div class="filter-item">
            <label>Kategori</label>
            <select v-model="filters.category" class="form-control">
              <option value="">Semua</option>
              <option value="Pelayanan Publik">Pelayanan Publik</option>
              <option value="Tata Kelola Pemerintahan">Tata Kelola Pemerintahan</option>
              <option value="Inovasi Daerah Lainnya">Inovasi Daerah Lainnya</option>
            </select>
          </div>
          
          <div class="filter-item">
            <label>Kecamatan</label>
            <select v-model="filters.kecamatan" class="form-control">
              <option value="">Pilih Kecamatan</option>
              <option value="Ampel">Ampel</option>
              <option value="Andong">Andong</option>
              <option value="Banyudono">Banyudono</option>
              <option value="Boyolali">Boyolali</option>
              <option value="Cepogo">Cepogo</option>
              <option value="Gladagsari">Gladagsari</option>
              <option value="Juwangi">Juwangi</option>
              <option value="Karanggede">Karanggede</option>
              <option value="Kemusu">Kemusu</option>
              <option value="Klego">Klego</option>
              <option value="Mojosongo">Mojosongo</option>
              <option value="Musuk">Musuk</option>
              <option value="Ngemplak">Ngemplak</option>
              <option value="Nogosari">Nogosari</option>
              <option value="Sambi">Sambi</option>
              <option value="Sawit">Sawit</option>
              <option value="Selo">Selo</option>
              <option value="Simo">Simo</option>
              <option value="Tamansari">Tamansari</option>
              <option value="Teras">Teras</option>
              <option value="Wonosegoro">Wonosegoro</option>
              <option value="Wonosamudro">Wonosamudro</option>
            </select>
          </div>
          
          <div class="filter-item">
            <label>Kelurahan</label>
            <select v-model="filters.kelurahan" class="form-control">
              <option value="">Pilih Kelurahan</option>
              <option value="Boyolali">Boyolali</option>
              <option value="Siswodipuran">Siswodipuran</option>
              <option value="Banaran">Banaran</option>
              <option value="Bayem">Bayem</option>
              <option value="Pulisen">Pulisen</option>
              <option value="Kalicacing">Kalicacing</option>
            </select>
          </div>
          
          <div class="filter-item">
            <label>Tahun</label>
            <select v-model="filters.tahun" class="form-control">
              <option value="">Pilih Tahun</option>
              <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>
          
          <div class="filter-item">
            <label>Bentuk Inovasi</label>
            <div class="checkbox-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="filters.bentuk" value="digital">
                Digital
              </label>
              <label class="checkbox-label">
                <input type="checkbox" v-model="filters.bentuk" value="non-digital">
                Non-Digital
              </label>
            </div>
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
  kecamatan: '',
  kelurahan: '',
  tahun: '2024',
  bentuk: [],
  kematangan: '',
  waktu: '',
  sort: 'newest'
})

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchName = p.nama_inovasi.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCategory = filters.value.category ? p.kategori === filters.value.category : true
    return matchName && matchCategory
  })
})

const years = computed(() => {
  const currentYear = new Date().getFullYear();
  const list = [];
  for (let y = currentYear; y >= 2015; y--) {
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

.category-pills {
  display: flex;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
  margin-top: 1.5rem;
}

.pill-btn {
  padding: 0.5rem 1.5rem;
  border-radius: 99px;
  border: 1px solid var(--border-color, #e2e8f0);
  background: var(--bg-white, #ffffff);
  color: var(--text-color, #333333);
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pill-btn:hover {
  border-color: var(--primary, #0056b3);
  color: var(--primary, #0056b3);
}

.pill-btn.active {
  background: #f0f7ff;
  border-color: var(--primary, #0056b3);
  color: var(--primary, #0056b3);
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

.filter-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.filter-header h3 {
  font-size: 1.125rem;
  font-weight: 700;
  margin: 0;
}

.filter-divider {
  border: 0;
  border-top: 1px solid var(--border-color, #e2e8f0);
  margin-bottom: 1.5rem;
}

.checkbox-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 0.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 400;
  color: var(--text-color, #333);
  cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
  width: 16px;
  height: 16px;
  border-radius: 4px;
  border: 1px solid var(--border-color, #cbd5e1);
  accent-color: var(--primary, #2563eb);
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
