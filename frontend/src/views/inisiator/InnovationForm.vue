<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <!-- Title Header Section -->
      <div class="form-header mb-4">
        <h1 class="form-title">Pengajuan Inovasi Baru</h1>
        <p class="form-subtitle">Lengkapi detail formulir di bawah ini untuk mendaftarkan inovasi Anda ke dalam sistem.</p>
      </div>

      <div v-if="msg" :class="['alert', isError ? 'alert-error' : 'alert-success', 'mb-4']">{{ msg }}</div>

      <form @submit.prevent="handleSubmit" class="innovation-form" enctype="multipart/form-data">
        <div class="form-grid">
          <!-- Left Column: Data Inisiator -->
          <div class="form-column">
            <h2 class="column-title">Data Inisiator</h2>
            
            <div class="form-group">
              <label>Nama Lengkap</label>
              <input 
                v-model="form.nama_inisiator" 
                type="text" 
                placeholder="Masukkan nama lengkap" 
                class="form-control" 
                required 
              />
            </div>

            <div class="form-group">
              <label>Jenis Inisiator</label>
              <select v-model="form.id_jenis_inisiator" class="form-control" required>
                <option value="">Pilih jenis inisiator</option>
                <option v-for="jenis in jenisInisiatorOptions" :key="jenis.id" :value="jenis.id">
                  {{ jenis.nama_jenis_inisiator }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label>Kontak (Email / No. HP)</label>
              <input 
                v-model="form.kontak" 
                type="text" 
                placeholder="Masukkan email atau nomor HP" 
                class="form-control" 
                required 
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="form-group">
                <label>Kecamatan</label>
                <select v-model="form.id_kecamatan" @change="onKecamatanChange" class="form-control" required>
                  <option value="">Pilih</option>
                  <option v-for="kec in kecamatanOptions" :key="kec.id" :value="kec.id">
                    {{ kec.nama_kecamatan }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Kelurahan</label>
                <select v-model="form.id_kelurahan" class="form-control" required :disabled="!form.id_kecamatan">
                  <option value="">Pilih</option>
                  <option v-for="kel in filteredKelurahans" :key="kel.id" :value="kel.id">
                    {{ kel.nama_kelurahan }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Right Column: Data Inovasi -->
          <div class="form-column">
            <h2 class="column-title">Data Inovasi</h2>

            <div class="form-group">
              <label>Nama Inovasi</label>
              <input 
                v-model="form.nama_inovasi" 
                type="text" 
                placeholder="Masukkan judul inovasi" 
                class="form-control" 
                required 
              />
            </div>

            <div class="form-group">
              <label>Deskripsi Singkat</label>
              <textarea 
                v-model="form.deskripsi" 
                placeholder="Jelaskan latar belakang, tujuan, dan manfaat inovasi..." 
                class="form-control textarea-desc" 
                rows="5"
                required
              ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="form-group">
                <label>Jenis Inovasi</label>
                <select v-model="form.id_bentuk" class="form-control" required>
                  <option value="">Pilih jenis</option>
                  <option v-for="bentuk in bentukOptions" :key="bentuk.id" :value="bentuk.id">
                    {{ bentuk.nama_bentuk }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Tahun Inisasi</label>
                <input 
                  v-model="form.tahun_inovasi" 
                  type="number" 
                  placeholder="YYYY" 
                  class="form-control" 
                  min="2000" 
                  max="2030" 
                  required 
                />
              </div>
            </div>

            <div class="form-group">
              <div class="label-wrapper">
                <label>Link Marketplace / Referensi</label>
                <span class="optional-badge">(Opsional)</span>
              </div>
              <input 
                v-model="form.link_marketplace" 
                type="url" 
                placeholder="https://..." 
                class="form-control" 
              />
            </div>
          </div>
        </div>

        <!-- Full Width: Media Dokumentasi -->
        <div class="media-section mt-4">
          <h2 class="column-title">Media Dokumentasi</h2>
          <div 
            class="upload-card" 
            :class="{ dragging: isDragging }"
            @dragover.prevent="onDragOver" 
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop"
            @click="triggerFileSelect"
          >
            <input 
              type="file" 
              ref="fileInput" 
              @change="onFileChange" 
              accept=".jpg,.jpeg,.png,.pdf" 
              style="display: none" 
            />
            <div class="upload-content">
              <div class="upload-icon-circle">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cloud-icon">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="17 8 12 3 7 8" />
                  <line x1="12" y1="3" x2="12" y2="15" />
                </svg>
              </div>
              <p class="upload-title">Klik untuk unggah atau seret file ke sini</p>
              <p class="upload-subtitle">Maksimal ukuran file 10MB (JPG, PNG, PDF)</p>
            </div>
          </div>

          <!-- File Preview -->
          <div v-if="selectedFile || existingFile" class="file-preview-card mt-3">
            <div class="file-info">
              <span class="file-icon">📄</span>
              <div>
                <p class="file-name">{{ selectedFile ? selectedFile.name : 'Dokumen Terunggah' }}</p>
                <p v-if="selectedFile" class="file-size">{{ formatFileSize(selectedFile.size) }}</p>
                <a v-else-if="existingFile" :href="existingFile" target="_blank" class="existing-link">Lihat file saat ini</a>
              </div>
            </div>
            <button type="button" @click="clearFile" class="btn-clear">✕</button>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="form-actions mt-5 pt-3">
          <button type="button" class="btn btn-outline-cancel" @click="goBack" :disabled="submitting">Batal</button>
          <button type="submit" class="btn btn-submit-blue" :disabled="submitting">
            {{ submitting ? 'Mengirim...' : (isEdit ? 'Simpan Perubahan' : 'Kirim Pengajuan') }}
          </button>
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

const opdOptions = ref([])
const bentukOptions = ref([])
const tahapanOptions = ref([])
const jenisInisiatorOptions = ref([])
const kecamatanOptions = ref([])
const allKelurahans = ref([])

const submitting = ref(false)
const msg = ref('')
const isError = ref(false)
const isDragging = ref(false)

const fileInput = ref(null)
const selectedFile = ref(null)
const existingFile = ref(null)

const form = ref({
  nama_inisiator: '',
  id_jenis_inisiator: '',
  kontak: '',
  id_kecamatan: '',
  id_kelurahan: '',
  nama_inovasi: '',
  deskripsi: '',
  id_bentuk: '',
  tahun_inovasi: new Date().getFullYear(),
  link_marketplace: ''
})

const filteredKelurahans = computed(() => {
  if (!form.value.id_kecamatan) return []
  return allKelurahans.value.filter(k => k.id_kecamatan === Number(form.value.id_kecamatan))
})

function onKecamatanChange() {
  form.value.id_kelurahan = ''
}

// Uploader interactions
function triggerFileSelect() {
  fileInput.value.click()
}

function onFileChange(e) {
  const files = e.target.files
  if (files && files[0]) {
    selectedFile.value = files[0]
  }
}

function onDragOver() {
  isDragging.value = true
}

function onDragLeave() {
  isDragging.value = false
}

function onDrop(e) {
  isDragging.value = false
  const files = e.dataTransfer.files
  if (files && files[0]) {
    selectedFile.value = files[0]
  }
}

function clearFile() {
  selectedFile.value = null
  existingFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

function goBack() {
  router.push('/inisiator')
}

async function handleSubmit() {
  submitting.value = true
  msg.value = ''
  
  try {
    const formData = new FormData()
    formData.append('nama_inisiator', form.value.nama_inisiator)
    formData.append('id_jenis_inisiator', form.value.id_jenis_inisiator)
    formData.append('kontak', form.value.kontak)
    formData.append('id_kelurahan', form.value.id_kelurahan)
    
    formData.append('nama_inovasi', form.value.nama_inovasi)
    formData.append('deskripsi', form.value.deskripsi)
    formData.append('id_bentuk', form.value.id_bentuk)
    formData.append('tahun_inovasi', form.value.tahun_inovasi)
    
    if (form.value.link_marketplace) {
      formData.append('link_marketplace', form.value.link_marketplace)
    }
    
    if (selectedFile.value) {
      formData.append('file_dokumentasi', selectedFile.value)
    }

    if (isEdit.value) {
      formData.append('_method', 'PUT') // Laravel spoofing for multipart PUT
      await api.post(`/inisiator/products/${route.query.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      msg.value = 'Inovasi berhasil diperbarui!'
    } else {
      await api.post('/inisiator/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      msg.value = 'Inovasi berhasil diajukan!'
    }

    isError.value = false
    setTimeout(() => router.push('/inisiator'), 1500)
  } catch (e) {
    msg.value = e.response?.data?.message || 'Gagal menyimpan inovasi. Pastikan seluruh kolom terisi dengan benar.'
    isError.value = true
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  let profile = null
  try {
    const resMeta = await api.get('/inisiator/metadata')
    opdOptions.value = resMeta.data.opds
    bentukOptions.value = resMeta.data.bentuk_inovasis
    tahapanOptions.value = resMeta.data.tahapan_inovasis
    jenisInisiatorOptions.value = resMeta.data.jenis_inisiators
    kecamatanOptions.value = resMeta.data.kecamatans
    allKelurahans.value = resMeta.data.kelurahan || resMeta.data.kelurahans || []
    profile = resMeta.data.inisiator_profile
    
    if (profile && !isEdit.value) {
      form.value.nama_inisiator = profile.nama_inisiator || ''
      form.value.kontak = profile.kontak || ''
      form.value.id_jenis_inisiator = profile.id_jenis_inisiator || ''
      form.value.id_kelurahan = profile.id_kelurahan || ''
      
      if (profile.id_kelurahan) {
        const kel = allKelurahans.value.find(k => k.id === profile.id_kelurahan)
        if (kel) {
          form.value.id_kecamatan = kel.id_kecamatan
        }
      }
    }
  } catch (e) {
    console.error('Gagal mengambil metadata:', e)
  }

  if (isEdit.value) {
    try {
      const res = await api.get(`/inisiator/products/${route.query.id}`)
      const data = res.data
      form.value.nama_inovasi = data.nama_inovasi
      form.value.deskripsi = data.deskripsi
      form.value.id_bentuk = data.id_bentuk
      form.value.tahun_inovasi = data.tahun_inovasi
      
      if (profile) {
        form.value.nama_inisiator = profile.nama_inisiator || ''
        form.value.kontak = profile.kontak || ''
        form.value.id_jenis_inisiator = profile.id_jenis_inisiator || ''
        form.value.id_kelurahan = profile.id_kelurahan || ''
        
        if (profile.id_kelurahan) {
          const kel = allKelurahans.value.find(k => k.id === profile.id_kelurahan)
          if (kel) {
            form.value.id_kecamatan = kel.id_kecamatan
          }
        }
      }

      const linkMedia = data.media_inovasi?.find(m => m.jenis_media === 'link')
      if (linkMedia) {
        form.value.link_marketplace = linkMedia.isi_konten
      }
      
      const fileMedia = data.media_inovasi?.find(m => m.jenis_media === 'file')
      if (fileMedia) {
        existingFile.value = fileMedia.isi_konten
      }
    } catch (e) {
      console.error('Gagal memuat produk inovasi:', e)
    }
  }
})
</script>

<style scoped>
.form-header {
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 1.5rem;
}

.form-title {
  font-size: 1.75rem;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.form-subtitle {
  font-size: 1rem;
  color: #64748b;
  margin: 0;
}

.innovation-form {
  background: #ffffff;
  border-radius: 12px;
  padding: 1rem 0;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
}

.form-column {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.column-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #334155;
}

.form-control {
  width: 100%;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  border: 1.5px solid #cbd5e1;
  border-radius: 8px;
  background-color: #ffffff;
  color: #1e293b;
  transition: all 0.2s ease-in-out;
}

.form-control::placeholder {
  color: #94a3b8;
}

.form-control:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.textarea-desc {
  resize: vertical;
  min-height: 120px;
}

.label-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.optional-badge {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 500;
}

/* Media Dokumentasi Uploader */
.media-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.upload-card {
  border: 2px dashed #3b82f6;
  background: #f0f7ff;
  border-radius: 12px;
  padding: 2.5rem 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.upload-card:hover, .upload-card.dragging {
  background: #e0f2fe;
  border-color: #2563eb;
  transform: translateY(-2px);
}

.upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
}

.upload-icon-circle {
  width: 48px;
  height: 48px;
  background: #ffffff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.cloud-icon {
  width: 24px;
  height: 24px;
  color: #2563eb;
}

.upload-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.upload-subtitle {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
}

/* File Preview Card */
.file-preview-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
}

.file-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.file-icon {
  font-size: 1.5rem;
}

.file-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #334155;
  margin: 0;
}

.file-size {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

.existing-link {
  font-size: 0.8rem;
  color: #2563eb;
  text-decoration: underline;
}

.btn-clear {
  background: none;
  border: none;
  color: #94a3b8;
  font-size: 1rem;
  cursor: pointer;
  padding: 0.25rem;
}

.btn-clear:hover {
  color: #ef4444;
}

/* Actions Footer */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  border-top: 2px solid #e2e8f0;
}

.btn-outline-cancel {
  background: #ffffff;
  border: 1.5px solid #cbd5e1;
  color: #2563eb;
  padding: 0.75rem 2.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-outline-cancel:hover {
  background: #f8fafc;
  border-color: #94a3b8;
}

.btn-submit-blue {
  background: #2563eb;
  border: none;
  color: #ffffff;
  padding: 0.75rem 2.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-submit-blue:hover {
  background: #1d4ed8;
}

.btn-submit-blue:disabled, .btn-outline-cancel:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
