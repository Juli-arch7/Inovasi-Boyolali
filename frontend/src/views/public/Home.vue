<template>
  <div>
    <section class="hero">
      <h1>Portal Inovasi Daerah</h1>
      <p>Temukan produk-produk inovasi terbaik dari berbagai daerah yang telah diverifikasi dan dikurasi oleh tim ahli kami.</p>
    </section>

    <div class="page fade-in">
      <div class="page-header">
        <h1>Produk Inovasi</h1>
        <p>Menampilkan {{ products.length }} produk inovasi yang telah dikurasi</p>
      </div>

      <div v-if="loading" style="text-align:center; padding:3rem; color:var(--text-muted)">
        Memuat data...
      </div>

      <div v-else-if="products.length === 0" style="text-align:center; padding:3rem; color:var(--text-muted)">
        Belum ada produk inovasi yang ditampilkan.
      </div>

      <div v-else class="grid-3">
        <div class="card" v-for="product in products" :key="product.id" @click="goToDetail(product.id)" style="cursor:pointer">
          <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:0.75rem">
            <span class="badge badge-approved">✓ Terverifikasi</span>
            <span style="font-size:0.8rem; color:var(--text-muted)">{{ product.tahun_inovasi }}</span>
          </div>
          <h3 style="font-size:1.1rem; font-weight:600; margin-bottom:0.5rem">{{ product.nama_inovasi }}</h3>
          <p style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:0.75rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden">
            {{ product.deskripsi || 'Tidak ada deskripsi.' }}
          </p>
          <div style="display:flex; gap:0.5rem; flex-wrap:wrap">
            <span style="font-size:0.75rem; padding:0.2rem 0.5rem; background:rgba(99,102,241,0.1); color:var(--primary-light); border-radius:4px">
              {{ product.bentuk_inovasi?.nama_bentuk || '-' }}
            </span>
            <span style="font-size:0.75rem; padding:0.2rem 0.5rem; background:rgba(14,165,233,0.1); color:var(--secondary); border-radius:4px">
              {{ product.opd?.nama_opd || '-' }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const products = ref([])
const loading = ref(true)

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

function goToDetail(id) {
  router.push(`/products/${id}`)
}
</script>
