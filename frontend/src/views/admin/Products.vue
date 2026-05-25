<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="page-title">Produk Inovasi</h1>
          <p class="text-muted">Daftar seluruh produk inovasi Kabupaten Boyolali yang telah disetujui (Approved).</p>
        </div>
      </div>

      <div class="card mb-4">
        <div class="search-filter-wrapper">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Cari nama inovasi..." 
            class="form-control search-input" 
          />
          <select v-model="filterOpd" class="form-control filter-select">
            <option value="">Semua OPD</option>
            <option v-for="opd in uniqueOpds" :key="opd" :value="opd">{{ opd }}</option>
          </select>
        </div>
      </div>

      <div class="card">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Inovasi</th>
                <th>Inisiator</th>
                <th>OPD</th>
                <th>Bentuk</th>
                <th>Tahapan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(product, index) in filteredProducts" :key="product.id">
                <td>{{ index + 1 }}</td>
                <td style="font-weight: 600;">{{ product.nama_inovasi }}</td>
                <td>{{ product.inisiator_profile?.nama_inisiator || '-' }}</td>
                <td>{{ product.opd?.nama_opd || '-' }}</td>
                <td><span class="badge-custom">{{ product.bentuk_inovasi?.nama_bentuk || '-' }}</span></td>
                <td>{{ product.tahapan_inovasi?.nama_tahapan || '-' }}</td>
                <td>
                  <button class="btn btn-outline btn-sm" @click="goToDetail(product.id)">Detail</button>
                </td>
              </tr>
              <tr v-if="filteredProducts.length === 0">
                <td colspan="7" class="text-center py-4">Tidak ada produk inovasi yang disetujui ditemukan.</td>
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

const router = useRouter()
const products = ref([])
const searchQuery = ref('')
const filterOpd = ref('')

async function loadProducts() {
  try {
    const res = await api.get('/admin/products')
    // Filter only approved ones
    products.value = res.data.filter(p => p.status_kurasi === 'approved')
  } catch (e) {
    console.error(e)
  }
}

const uniqueOpds = computed(() => {
  const opds = products.value.map(p => p.opd?.nama_opd).filter(Boolean)
  return [...new Set(opds)]
})

const filteredProducts = computed(() => {
  return products.value.filter(product => {
    const matchesSearch = product.nama_inovasi.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesOpd = !filterOpd.value || product.opd?.nama_opd === filterOpd.value
    return matchesSearch && matchesOpd
  })
})

function goToDetail(id) {
  router.push(`/admin/verifikasi/${id}`)
}

onMounted(loadProducts)
</script>

<style scoped>
.d-flex {
  display: flex;
}
.justify-content-between {
  justify-content: space-between;
}
.align-items-center {
  align-items: center;
}
.search-filter-wrapper {
  display: flex;
  gap: 1rem;
}
.search-input {
  flex: 2;
}
.filter-select {
  flex: 1;
  min-width: 200px;
}
.badge-custom {
  background: var(--primary-light);
  color: var(--primary);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}
</style>
