<template>
  <div class="home-page fade-in">
    <!-- Centered Search Section -->
    <section class="hero-section">
      <h1 class="discover-title">
        TEMUKAN <span class="blue-text">INOVASI</span>
        <span class="decor-line"></span>
      </h1>
      
      <!-- Pill Search Bar -->
      <div class="search-container">
        <div class="search-pill">
          <svg xmlns="http://www.w3.org/2000/svg" class="search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Cari inovasi..." 
            class="search-input-field"
            @keyup.enter="applyFilters"
          />
          <button class="search-button-pill" @click="applyFilters">Cari</button>
        </div>
      </div>
    </section>

    <!-- Main Grid Content -->
    <div class="main-content-layout">
      <!-- Left Column: Filter Panel -->
      <aside class="filter-sidebar card">
        <div class="filter-header">
          <svg xmlns="http://www.w3.org/2000/svg" class="filter-title-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
          </svg>
          <h2 class="filter-title">Filter</h2>
        </div>

        <div class="filter-form">
          <!-- Kategori -->
          <div class="form-group">
            <label>Kategori</label>
            <select v-model="filterForm.bentuk" class="form-control">
              <option value="">Semua Kategori</option>
              <option v-for="b in bentukOptions" :key="b.id" :value="b.nama_bentuk">
                {{ b.nama_bentuk }}
              </option>
            </select>
          </div>

          <!-- Kecamatan -->
          <div class="form-group">
            <label>Kecamatan</label>
            <select v-model="filterForm.kecamatan" @change="onKecamatanChange" class="form-control">
              <option value="">Pilih Kecamatan</option>
              <option v-for="kec in kecamatanOptions" :key="kec.id" :value="kec.id">
                {{ kec.nama_kecamatan }}
              </option>
            </select>
          </div>

          <!-- Kelurahan -->
          <div class="form-group">
            <label>Kelurahan</label>
            <select v-model="filterForm.kelurahan" class="form-control" :disabled="!filterForm.kecamatan">
              <option value="">Pilih Kelurahan</option>
              <option v-for="kel in filteredKelurahans" :key="kel.id" :value="kel.id">
                {{ kel.nama_kelurahan }}
              </option>
            </select>
          </div>

          <!-- Tahun -->
          <div class="form-group">
            <label>Tahun</label>
            <select v-model="filterForm.tahun" class="form-control">
              <option value="">Semua Tahun</option>
              <option v-for="yr in yearOptions" :key="yr" :value="yr">{{ yr }}</option>
            </select>
          </div>

          <!-- Bentuk Inovasi (Digital/Non-Digital Checkbox) -->
          <div class="form-group">
            <label>Bentuk Inovasi</label>
            <div class="checkbox-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="filterForm.digital" class="checkbox-input" />
                <span class="custom-checkbox"></span>
                Digital
              </label>
              <label class="checkbox-label">
                <input type="checkbox" v-model="filterForm.nonDigital" class="checkbox-input" />
                <span class="custom-checkbox"></span>
                Non-Digital
              </label>
            </div>
          </div>

          <!-- Terapkan Button -->
          <button class="btn btn-primary w-full btn-terapkan" @click="applyFilters">
            Terapkan Filter
          </button>
        </div>
      </aside>

      <!-- Right Column: Results & Pills -->
      <main class="results-area">
        <!-- Horizontal Category Pills -->
        <div class="category-pills">
          <button 
            v-for="pill in ['Semua', 'Pelayanan Publik', 'Tata Kelola Pemerintahan', 'Inovasi Daerah Lainnya']" 
            :key="pill"
            :class="['pill-btn', { active: activePill === pill }]"
            @click="selectPill(pill)"
          >
            {{ pill }}
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5 loading-spinner">
          <div class="spinner"></div>
          <p class="mt-2 text-muted">Memuat data inovasi...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredProducts.length === 0" class="empty-state card text-center py-5">
          <span class="empty-icon">📂</span>
          <h3>Tidak ada inovasi ditemukan</h3>
          <p class="text-muted">Coba ubah filter pencarian Anda untuk melihat produk inovasi lainnya.</p>
        </div>

        <!-- Cards Grid -->
        <div v-else class="grid-3 innovation-grid">
          <div 
            class="innovation-card card" 
            v-for="product in filteredProducts" 
            :key="product.id"
            @click="goToDetail(product.id)"
          >
            <!-- Card Header Image -->
            <div class="card-cover">
              <!-- Digital Pattern Banner Cover SVG -->
              <svg v-if="product.is_digital" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 200" class="cover-svg digital-svg">
                <defs>
                  <linearGradient id="digGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#1e3a8a" />
                    <stop offset="50%" stop-color="#2563eb" />
                    <stop offset="100%" stop-color="#0ea5e9" />
                  </linearGradient>
                </defs>
                <rect width="100%" height="100%" fill="url(#digGrad)" />
                <circle cx="200" cy="100" r="80" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="2" />
                <circle cx="200" cy="100" r="50" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="1.5" />
                <!-- Connected Tech Nodes Pattern -->
                <path d="M50 50 L120 100 L200 60 L280 120 L350 70" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3" />
                <path d="M80 150 L150 90 L230 140 L320 80" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2" />
                <circle cx="120" cy="100" r="6" fill="#0ea5e9" />
                <circle cx="200" cy="60" r="6" fill="#3b82f6" />
                <circle cx="280" cy="120" r="6" fill="#10b981" />
                <circle cx="150" cy="90" r="4" fill="#ffffff" />
                <circle cx="320" cy="80" r="5" fill="#f59e0b" />
                <text x="20" y="40" fill="rgba(255,255,255,0.25)" font-size="12" font-family="monospace">0101010110</text>
                <text x="320" y="170" fill="rgba(255,255,255,0.25)" font-size="12" font-family="monospace">SYSTEM.IO</text>
              </svg>

              <!-- Non-Digital Pattern Banner Cover SVG -->
              <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 200" class="cover-svg nd-svg">
                <defs>
                  <linearGradient id="ndGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0f766e" />
                    <stop offset="50%" stop-color="#0d9488" />
                    <stop offset="100%" stop-color="#14b8a6" />
                  </linearGradient>
                </defs>
                <rect width="100%" height="100%" fill="url(#ndGrad)" />
                <!-- Modern Geometric Waves -->
                <path d="M 0,100 C 100,50 150,150 250,100 C 350,50 380,120 400,80 L 400,200 L 0,200 Z" fill="rgba(255,255,255,0.06)" />
                <path d="M 0,130 C 80,90 180,170 280,120 C 380,70 390,140 400,110 L 400,200 L 0,200 Z" fill="rgba(255,255,255,0.08)" />
                <circle cx="100" cy="70" r="25" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="2" />
                <circle cx="280" cy="80" r="35" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="3" />
                <path d="M80 70 H 120 M 100 50 V 90" stroke="rgba(255,255,255,0.15)" stroke-width="2" />
              </svg>

              <!-- Pill badge top right of cover -->
              <span :class="['pill-badge', product.is_digital ? 'badge-digital' : 'badge-non-digital']">
                {{ product.is_digital ? 'Digital' : 'Non-Digital' }}
              </span>
            </div>

            <!-- Card Body -->
            <div class="card-body">
              <h3 class="card-title">{{ product.nama_inovasi }}</h3>
              
              <!-- OPD Name with Thumbs-up / Agency Icon -->
              <div class="card-opd">
                <svg xmlns="http://www.w3.org/2000/svg" class="opd-icon" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
                <span class="opd-name">{{ product.opd?.nama_opd || 'Umum / Instansi' }}</span>
              </div>

              <!-- Card Footer -->
              <div class="card-footer-item">
                <span class="card-year">Tahun {{ product.tahun_inovasi }}</span>
                <span class="card-arrow">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="arrow-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Page Footer -->
    <footer class="footer mt-5">
      <div class="footer-logo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="logo-icon inline-icon">
          <path d="M12 2L1 7h22L12 2zm10 7H2v2h20V9zM4 12h3v7H4v-7zm6 0h3v7h-3v-7zm6 0h3v7h-3v-7zm-4 8h10v2H2v-2h10z"/>
        </svg>
        BAPPERIDA BOYOLALI
      </div>
      <div class="footer-links">
        <a href="#">Syarat & Ketentuan</a>
        <a href="#">Kontak Kami</a>
      </div>
      <div class="footer-copyright">
        © 2026 Portal Inovasi BAPPERIDA Boyolali. Seluruh hak cipta dilindungi.
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const products = ref([])
const loading = ref(true)

// Filters Form State
const searchQuery = ref('')
const activePill = ref('Semua')

const filterForm = ref({
  bentuk: '',
  kecamatan: '',
  kelurahan: '',
  tahun: '',
  digital: false,
  nonDigital: false
})

// Options list loaded from metadata
const bentukOptions = ref([])
const kecamatanOptions = ref([])
const kelurahanOptions = ref([])
const yearOptions = ref([])

// Unique Kelurahans filtered by selected Kecamatan
const filteredKelurahans = computed(() => {
  if (!filterForm.value.kecamatan) return []
  return kelurahanOptions.value.filter(k => k.id_kecamatan === Number(filterForm.value.kecamatan))
})

// Dynamically filter products on the client side
const filteredProducts = computed(() => {
  return products.value.filter(p => {
    // 1. Search Query Filter
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase()
      const matchName = p.nama_inovasi.toLowerCase().includes(q)
      const matchDesc = p.deskripsi && p.deskripsi.toLowerCase().includes(q)
      const matchOpd = p.opd?.nama_opd && p.opd.nama_opd.toLowerCase().includes(q)
      if (!matchName && !matchDesc && !matchOpd) return false
    }

    // 2. Horizontal Pill Category Filter
    if (activePill.value !== 'Semua') {
      const bName = p.bentuk_inovasi?.nama_bentuk || ''
      if (activePill.value === 'Pelayanan Publik' && !bName.includes('Pelayanan Publik')) return false
      if (activePill.value === 'Tata Kelola Pemerintahan' && !bName.includes('Tata Kelola')) return false
      if (activePill.value === 'Inovasi Daerah Lainnya' && (bName.includes('Pelayanan Publik') || bName.includes('Tata Kelola'))) return false
    }

    // 3. Dropdown Bentuk (Kategori) Filter
    if (filterForm.value.bentuk) {
      if (p.bentuk_inovasi?.nama_bentuk !== filterForm.value.bentuk) return false
    }

    // 4. Dropdown Kecamatan Filter
    if (filterForm.value.kecamatan) {
      if (p.inisiator_profile?.kelurahan?.kecamatan?.id !== Number(filterForm.value.kecamatan)) return false
    }

    // 5. Dropdown Kelurahan Filter
    if (filterForm.value.kelurahan) {
      if (p.inisiator_profile?.kelurahan?.id !== Number(filterForm.value.kelurahan)) return false
    }

    // 6. Dropdown Tahun Filter
    if (filterForm.value.tahun) {
      if (p.tahun_inovasi !== Number(filterForm.value.tahun)) return false
    }

    // 7. Checkboxes Shape Filter (Digital / Non-Digital)
    const isDigChecked = filterForm.value.digital
    const isNonDigChecked = filterForm.value.nonDigital
    if (isDigChecked && !isNonDigChecked && !p.is_digital) return false
    if (!isDigChecked && isNonDigChecked && p.is_digital) return false

    return true
  })
})

function onKecamatanChange() {
  filterForm.value.kelurahan = ''
}

function selectPill(pill) {
  activePill.value = pill
}

function applyFilters() {
  // Computed property filteredProducts takes care of reactive rendering dynamically!
}

function goToDetail(id) {
  router.push(`/products/${id}`)
}

async function loadData() {
  loading.value = true
  try {
    // Load products
    const resProd = await api.get('/public/products')
    products.value = resProd.data

    // Extract years dynamically for year filter dropdown
    const years = resProd.data.map(p => p.tahun_inovasi).filter(Boolean)
    yearOptions.value = [...new Set(years)].sort((a, b) => b - a)

    // Load filter metadata
    const resMeta = await api.get('/public/metadata')
    bentukOptions.value = resMeta.data.bentuk_inovasis || []
    kecamatanOptions.value = resMeta.data.kecamatans || []
    kelurahanOptions.value = resMeta.data.kelurahans || []
  } catch (e) {
    console.error('Failed to load home page data:', e)
  } finally {
    loading.value = false
  }
}

onMounted(loadData)
</script>

<style scoped>
.home-page {
  padding: 0;
  max-width: 100%;
}

/* ─── Hero Section ─── */
.hero-section {
  text-align: center;
  padding: 4.5rem 2rem 3.5rem 2rem;
  background: linear-gradient(180deg, #eff6ff 0%, rgba(244, 246, 250, 0) 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
}

.discover-title {
  font-size: 2.75rem;
  font-weight: 850;
  color: #1e293b;
  margin-bottom: 2rem;
  letter-spacing: -0.02em;
  position: relative;
  display: inline-block;
}

.blue-text {
  color: var(--primary);
}

.decor-line {
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 140px;
  height: 4px;
  background: var(--primary);
  border-radius: 99px;
}

/* ─── Search Container ─── */
.search-container {
  width: 100%;
  max-width: 720px;
  margin-top: 1rem;
}

.search-pill {
  background: #ffffff;
  border-radius: 999px;
  padding: 0.5rem 0.5rem 0.5rem 1.5rem;
  display: flex;
  align-items: center;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  border: 1px solid var(--border);
  transition: var(--transition);
}

.search-pill:focus-within {
  border-color: var(--primary-light);
  box-shadow: 0 12px 30px rgba(37, 99, 235, 0.1);
}

.search-icon {
  width: 22px;
  height: 22px;
  color: var(--text-muted);
  flex-shrink: 0;
  margin-right: 0.75rem;
}

.search-input-field {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 1.05rem;
  color: var(--text-primary);
  font-family: inherit;
  outline: none;
}

.search-input-field::placeholder {
  color: var(--text-muted);
}

.search-button-pill {
  background: var(--primary);
  color: #ffffff;
  font-size: 0.95rem;
  font-weight: 600;
  border: none;
  padding: 0.75rem 2.25rem;
  border-radius: 999px;
  cursor: pointer;
  transition: var(--transition);
}

.search-button-pill:hover {
  background: var(--primary-dark);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

/* ─── Two Column Layout ─── */
.main-content-layout {
  max-width: 1350px;
  margin: 0 auto;
  padding: 0 3rem 4rem 3rem;
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 3rem;
}

/* ─── Filter Sidebar ─── */
.filter-sidebar {
  height: fit-content;
  padding: 1.75rem;
  background: #ffffff;
  border-radius: var(--radius);
  border: 1px solid var(--border);
}

.filter-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1.5px solid var(--border);
}

.filter-title-icon {
  width: 20px;
  height: 20px;
  color: var(--primary);
}

.filter-title {
  font-size: 1.15rem;
  font-weight: 750;
  color: var(--text-primary);
}

.filter-form .form-group {
  margin-bottom: 1.25rem;
}

.filter-form label {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--text-secondary);
  margin-bottom: 0.4rem;
}

.checkbox-group {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
  margin-top: 0.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  position: relative;
  padding-left: 1.75rem;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500 !important;
  color: var(--text-primary);
  user-select: none;
}

.checkbox-input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.custom-checkbox {
  position: absolute;
  top: 1px;
  left: 0;
  height: 18px;
  width: 18px;
  background-color: #ffffff;
  border: 1.5px solid var(--border);
  border-radius: 4px;
  transition: var(--transition);
}

.checkbox-label:hover .checkbox-input ~ .custom-checkbox {
  border-color: var(--primary-light);
}

.checkbox-input:checked ~ .custom-checkbox {
  background-color: var(--primary);
  border-color: var(--primary);
}

.custom-checkbox:after {
  content: "";
  position: absolute;
  display: none;
}

.checkbox-input:checked ~ .custom-checkbox:after {
  display: block;
}

.checkbox-label .custom-checkbox:after {
  left: 5px;
  top: 2px;
  width: 5px;
  height: 9px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.btn-terapkan {
  margin-top: 1.75rem;
  border-radius: 8px;
  padding: 0.75rem;
}

/* ─── Results Content Area ─── */
.results-area {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* Category Pills Nav */
.category-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.pill-btn {
  background: #ffffff;
  border: 1px solid var(--border);
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 600;
  padding: 0.55rem 1.25rem;
  border-radius: 99px;
  cursor: pointer;
  transition: var(--transition);
}

.pill-btn:hover {
  border-color: var(--primary-light);
  color: var(--primary);
  background: rgba(37, 99, 235, 0.01);
}

.pill-btn.active {
  background: rgba(37, 99, 235, 0.08);
  border-color: var(--primary);
  color: var(--primary);
}

/* ─── Cards Grid ─── */
.innovation-card {
  padding: 0 !important;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  cursor: pointer;
}

.card-cover {
  position: relative;
  width: 100%;
  height: 160px;
  background: #f1f5f9;
  overflow: hidden;
}

.cover-svg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transition);
}

.innovation-card:hover .cover-svg {
  transform: scale(1.05);
}

.pill-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.badge-digital {
  background: rgba(255, 255, 255, 0.95);
  color: var(--primary);
}

.badge-non-digital {
  background: rgba(255, 255, 255, 0.95);
  color: #0f766e;
}

.card-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.card-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  height: 2.9rem;
}

.card-opd {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.opd-icon {
  width: 16px;
  height: 16px;
  color: var(--primary-light);
  flex-shrink: 0;
  margin-top: 2px;
}

.opd-name {
  font-size: 0.8rem;
  font-weight: 500;
  color: var(--text-secondary);
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-footer-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid var(--border);
}

.card-year {
  background: #f1f5f9;
  color: var(--text-secondary);
  font-size: 0.775rem;
  font-weight: 600;
  padding: 0.25rem 0.6rem;
  border-radius: 4px;
}

.card-arrow {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  transition: var(--transition);
}

.innovation-card:hover .card-arrow {
  transform: translateX(4px);
}

.arrow-icon {
  width: 18px;
  height: 18px;
}

/* ─── Footer Extra ─── */
.inline-icon {
  width: 22px;
  height: 22px;
  vertical-align: middle;
  margin-right: 0.5rem;
}

/* Spinner */
.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--border);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 1rem;
}

/* ─── Responsive ─── */
@media (max-width: 992px) {
  .main-content-layout {
    grid-template-columns: 1fr;
    padding: 0 1.5rem 3rem 1.5rem;
    gap: 2rem;
  }
  .hero-section {
    padding: 3rem 1.5rem 2rem 1.5rem;
  }
  .discover-title {
    font-size: 2.25rem;
  }
}
</style>
