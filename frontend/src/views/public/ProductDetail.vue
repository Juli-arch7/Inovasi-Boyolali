<template>
  <div class="page fade-in">
    <div v-if="loading" style="text-align:center; padding:3rem; color:var(--text-muted)">Memuat detail...</div>
    <div v-else-if="!product" style="text-align:center; padding:3rem; color:var(--text-muted)">Produk tidak ditemukan.</div>
    <div v-else>
      <button class="btn btn-outline btn-sm" @click="$router.push('/')" style="margin-bottom:1.5rem">← Kembali</button>

      <div class="card" style="max-width:800px">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem">
          <span class="badge badge-approved">✓ Terverifikasi</span>
          <span style="font-size:0.85rem; color:var(--text-muted)">Tahun {{ product.tahun_inovasi }}</span>
        </div>

        <h1 style="font-size:1.75rem; font-weight:700; margin-bottom:0.75rem">{{ product.nama_inovasi }}</h1>

        <p style="color:var(--text-secondary); margin-bottom:1.5rem; line-height:1.8">
          {{ product.deskripsi || 'Tidak ada deskripsi.' }}
        </p>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem">
          <div>
            <span style="font-size:0.8rem; color:var(--text-muted); display:block">OPD</span>
            <span style="font-weight:500">{{ product.opd?.nama_opd || '-' }}</span>
          </div>
          <div>
            <span style="font-size:0.8rem; color:var(--text-muted); display:block">Bentuk Inovasi</span>
            <span style="font-weight:500">{{ product.bentuk_inovasi?.nama_bentuk || '-' }}</span>
          </div>
          <div>
            <span style="font-size:0.8rem; color:var(--text-muted); display:block">Tahapan</span>
            <span style="font-weight:500">{{ product.tahapan_inovasi?.nama_tahapan || '-' }}</span>
          </div>
          <div>
            <span style="font-size:0.8rem; color:var(--text-muted); display:block">Digital</span>
            <span style="font-weight:500">{{ product.is_digital ? 'Ya' : 'Tidak' }}</span>
          </div>
          <div>
            <span style="font-size:0.8rem; color:var(--text-muted); display:block">Inisiator</span>
            <span style="font-weight:500">{{ product.inisiator_profile?.nama_inisiator || '-' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const product = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get(`/public/products/${route.params.id}`)
    product.value = res.data
  } catch (e) {
    console.error('Failed to load product', e)
  } finally {
    loading.value = false
  }
})
</script>
