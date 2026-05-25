<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4">
        <h1 class="page-title">Dashboard Overview</h1>
        <p class="text-muted">Pantau data dan statistik inovasi terbaru di Kabupaten Boyolali.</p>
      </div>

      <div class="grid grid-cols-3 mb-4">
        <div class="stat-card card">
          <div class="stat-icon"><i class='bx bx-bar-chart-alt-2'></i></div>
          <div class="stat-info">
            <span class="stat-label">Total Inovasi</span>
            <h2 class="stat-value">124.500</h2>
          </div>
        </div>
        <div class="stat-card card">
          <div class="stat-icon"><i class='bx bx-building-house'></i></div>
          <div class="stat-info">
            <span class="stat-label">Total OPD</span>
            <h2 class="stat-value">8.402</h2>
          </div>
        </div>
        <div class="stat-card card">
          <div class="stat-icon"><i class='bx bx-buildings'></i></div>
          <div class="stat-info">
            <span class="stat-label">Unit Kerja</span>
            <h2 class="stat-value">342</h2>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center mb-4">
          <h3 class="section-title">Statistik Inovasi</h3>
          <select class="form-control" style="width: 150px;">
            <option>Tahun 2024</option>
          </select>
        </div>
        <div class="chart-placeholder" style="height: 300px; background: var(--bg-gray); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
          <div class="bars" style="display: flex; gap: 20px; align-items: flex-end; height: 80%;">
            <div class="bar" style="width: 30px; height: 40%; background: var(--primary);"></div>
            <div class="bar" style="width: 30px; height: 70%; background: var(--primary);"></div>
            <div class="bar" style="width: 30px; height: 50%; background: var(--primary);"></div>
            <div class="bar" style="width: 30px; height: 90%; background: var(--primary);"></div>
            <div class="bar" style="width: 30px; height: 60%; background: var(--primary);"></div>
            <div class="bar" style="width: 30px; height: 30%; background: var(--primary);"></div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const products = ref([])

async function loadProducts() {
  try {
    const res = await api.get('/admin/products')
    products.value = res.data
  } catch (e) {
    console.error(e)
  }
}

onMounted(loadProducts)
</script>

<style scoped>
.stat-card {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 1.5rem;
}

.stat-icon {
  width: 50px;
  height: 50px;
  background: var(--primary-light);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
  color: var(--primary);
}

.stat-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-muted);
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 800;
  margin: 0;
  color: var(--text-main);
}

.section-title {
  font-size: 1.125rem;
  font-weight: 700;
  margin: 0;
}

.d-flex { display: flex; }
.justify-content-between { justify-content: space-between; }
.align-items-center { align-items: center; }
</style>
