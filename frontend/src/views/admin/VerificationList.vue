<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4">
        <h1 class="page-title">Verifikasi Inovasi</h1>
        <p class="text-muted">Kelola dan verifikasi ajuan inovasi yang masuk ke sistem.</p>
      </div>

      <div class="card">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Inovasi</th>
                <th>Inisiator</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(product, index) in products" :key="product.id">
                <td>{{ index + 1 }}</td>
                <td style="font-weight: 600;">{{ product.nama_inovasi }}</td>
                <td>{{ product.inisiator_profile?.nama_inisiator || '-' }}</td>
                <td><span class="badge-custom">{{ product.is_digital ? 'Digital' : 'Non Digital' }}</span></td>
                <td>
                  <span :class="['badge', `badge-${product.status_kurasi}`]">
                    {{ product.status_kurasi }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-outline btn-sm" @click="goToDetail(product.id)">Detail</button>
                </td>
              </tr>
              <tr v-if="products.length === 0">
                <td colspan="6" class="text-center py-4">Belum ada ajuan untuk diverifikasi.</td>
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const products = ref([])

async function loadProducts() {
  try {
    const res = await api.get('/admin/products')
    products.value = res.data
  } catch (e) {
    console.error(e)
  }
}

function goToDetail(id) {
  router.push(`/admin/verifikasi/${id}`)
}

onMounted(loadProducts)
</script>

<style scoped>
.badge-custom {
  background: var(--primary-light);
  color: var(--primary);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}
</style>
