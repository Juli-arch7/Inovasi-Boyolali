<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="page-title">Produk Inovasi</h1>
          <p class="text-muted">Daftar seluruh produk inovasi Kabupaten Boyolali yang telah disetujui.</p>
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
          <select v-model="filterStatus" class="form-control filter-select">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
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
                <th>Status</th>
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
                  <span :class="['status-pill', product.is_active ? 'status-active' : 'status-inactive']">
                    {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="actions-cell">
                  <button class="btn btn-outline btn-sm" @click="goToDetail(product.id)">Detail</button>
                  <button
                    :class="['btn', 'btn-sm', product.is_active ? 'btn-deactivate' : 'btn-activate']"
                    @click="toggleActive(product)"
                    :disabled="toggling === product.id"
                  >
                    {{ product.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                  </button>
                </td>
              </tr>
              <tr v-if="filteredProducts.length === 0">
                <td colspan="8" class="text-center py-4">Tidak ada produk inovasi yang ditemukan.</td>
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
const filterStatus = ref('')
const toggling = ref(null)

async function loadProducts() {
  try {
    const res = await api.get('/admin/products')
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
    const matchesSearch = product.nama_inovasi?.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesOpd = !filterOpd.value || product.opd?.nama_opd === filterOpd.value
    const matchesStatus =
      filterStatus.value === '' ||
      (filterStatus.value === 'active' && product.is_active) ||
      (filterStatus.value === 'inactive' && !product.is_active)
    return matchesSearch && matchesOpd && matchesStatus
  })
})

function goToDetail(id) {
  router.push(`/admin/verifikasi/${id}`)
}

async function toggleActive(product) {
  if (!confirm(`Apakah Anda yakin ingin ${product.is_active ? 'menonaktifkan' : 'mengaktifkan'} produk ini?`)) return
  toggling.value = product.id
  try {
    const res = await api.put(`/admin/products/${product.id}/toggle-active`)
    // Update locally
    const idx = products.value.findIndex(p => p.id === product.id)
    if (idx !== -1) {
      products.value[idx] = { ...products.value[idx], is_active: res.data.product.is_active }
    }
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal mengubah status produk.')
  } finally {
    toggling.value = null
  }
}

onMounted(loadProducts)
</script>

<style scoped>
.d-flex { display: flex; }
.justify-content-between { justify-content: space-between; }
.align-items-center { align-items: center; }

.search-filter-wrapper {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.search-input {
  flex: 2;
  min-width: 200px;
}

.filter-select {
  flex: 1;
  min-width: 160px;
}

.badge-custom {
  background: var(--primary-light);
  color: var(--primary);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-pill {
  padding: 0.25rem 0.75rem;
  border-radius: 99px;
  font-size: 0.75rem;
  font-weight: 600;
  display: inline-block;
}

.status-active {
  background: #dcfce7;
  color: #16a34a;
}

.status-inactive {
  background: #fee2e2;
  color: #dc2626;
}

.actions-cell {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.btn-deactivate {
  background: transparent;
  color: #dc2626;
  border: 1px solid #dc2626;
  border-radius: 6px;
  padding: 0.3rem 0.75rem;
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-deactivate:hover:not(:disabled) {
  background: #fef2f2;
}

.btn-activate {
  background: transparent;
  color: #16a34a;
  border: 1px solid #16a34a;
  border-radius: 6px;
  padding: 0.3rem 0.75rem;
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-activate:hover:not(:disabled) {
  background: #f0fdf4;
}

.btn-deactivate:disabled,
.btn-activate:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
