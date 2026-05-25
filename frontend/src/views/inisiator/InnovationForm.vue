<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4">
        <h1 class="page-title">{{ isEdit ? 'Edit Inovasi' : 'Pengajuan Inovasi Baru' }}</h1>
        <p class="text-muted">Lengkapi formulir di bawah ini untuk mengajukan inovasi Anda.</p>
      </div>

      <div v-if="msg" :class="['alert', isError ? 'alert-error' : 'alert-success']">{{ msg }}</div>

      <form @submit.prevent="handleSubmit">
        <div class="grid grid-cols-2">
          <!-- Data Inisiator -->
          <div class="card">
            <h3 class="section-title mb-4">Data Inisiator</h3>
            <div class="form-group">
              <label>Profil Inisiator</label>
              <select class="form-control" v-model="form.id_inisiator" required>
                <option value="">Pilih Profil</option>
                <!-- Populate from API -->
              </select>
            </div>
            <div class="form-group">
              <label>OPD</label>
              <select class="form-control" v-model="form.id_opd" required>
                <option value="">Pilih OPD</option>
                <!-- Populate from API -->
              </select>
            </div>
          </div>

          <!-- Data Inovasi -->
          <div class="card">
            <h3 class="section-title mb-4">Data Inovasi</h3>
            <div class="form-group">
              <label>Nama Inovasi</label>
              <input type="text" class="form-control" v-model="form.nama_inovasi" placeholder="Nama Inovasi Anda" required />
            </div>
            <div class="form-group">
              <label>Tahapan Inovasi</label>
              <select class="form-control" v-model="form.id_tahapan" required>
                <option value="">Pilih Tahapan</option>
                <!-- Populate from API -->
              </select>
            </div>
            <div class="form-group">
              <label>Bentuk Inovasi</label>
              <select class="form-control" v-model="form.id_bentuk" required>
                <option value="">Pilih Bentuk</option>
                <!-- Populate from API -->
              </select>
            </div>
            <div class="form-group">
              <label>Tahun Inovasi</label>
              <input type="number" class="form-control" v-model="form.tahun_inovasi" :min="2000" :max="2030" required />
            </div>
          </div>
        </div>

        <div class="card mt-4">
          <h3 class="section-title mb-4">Deskripsi & Detail</h3>
          <div class="form-group">
            <label>Deskripsi Inovasi</label>
            <textarea class="form-control" v-model="form.deskripsi" rows="5" placeholder="Jelaskan inovasi Anda secara detail..."></textarea>
          </div>
          <div class="form-group">
            <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer">
              <input type="checkbox" v-model="form.is_digital" />
              Inovasi Digital
            </label>
          </div>
        </div>

        <div class="form-actions mt-4">
          <button type="button" class="btn btn-outline" @click="$router.push('/inisiator')">BATAL</button>
          <div style="display: flex; gap: 0.5rem;">
            <button type="button" class="btn btn-outline" @click="saveDraft" :disabled="submitting">SIMPAN DRAFT</button>
            <button type="submit" class="btn btn-primary" :disabled="submitting">{{ submitting ? 'MENGIRIM...' : 'KIRIM PENGAJUAN' }}</button>
          </div>
        </div>
      </form>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.query.id)
const submitting = ref(false)
const msg = ref('')
const isError = ref(false)

const form = ref({
  nama_inovasi: '',
  deskripsi: '',
  tahun_inovasi: new Date().getFullYear(),
  id_opd: '',
  id_bentuk: '',
  id_tahapan: '',
  id_inisiator: '',
  is_digital: false
})

async function handleSubmit() {
  submitting.value = true
  msg.value = ''
  try {
    const payload = { ...form.value, is_digital: form.value.is_digital ? 1 : 0 }
    if (isEdit.value) {
      await api.put(`/inisiator/products/${route.query.id}`, payload)
      msg.value = 'Inovasi berhasil diperbarui!'
    } else {
      await api.post('/inisiator/products', payload)
      msg.value = 'Inovasi berhasil diajukan!'
    }
    isError.value = false
    setTimeout(() => router.push('/inisiator'), 1500)
  } catch (e) {
    msg.value = e.response?.data?.message || 'Gagal menyimpan inovasi.'
    isError.value = true
  } finally {
    submitting.value = false
  }
}

async function saveDraft() {
  // Same logic but maybe different status on backend
  handleSubmit()
}

onMounted(async () => {
  if (isEdit.value) {
    try {
      const res = await api.get(`/inisiator/products/${route.query.id}`)
      const data = res.data
      form.value = {
        ...data,
        is_digital: !!data.is_digital
      }
    } catch (e) {
      console.error(e)
    }
  }
})
</script>

<style scoped>
.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.section-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--primary);
  border-bottom: 2px solid var(--primary-light);
  padding-bottom: 0.5rem;
}
</style>
