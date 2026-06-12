<template>
  <div class="dashboard-layout">
    <Sidebar />
    <main class="content-area">
      <div class="page-header mb-4">
        <h1 class="page-title">{{ isEdit ? 'Edit Inovasi' : 'Pengajuan Inovasi Baru' }}</h1>
        <p class="text-muted">Lengkapi formulir di bawah ini untuk mengajukan inovasi Anda.</p>
      </div>

      <div v-if="msg" :class="['alert', isError ? 'alert-error' : 'alert-success', 'mb-4']">{{ msg }}</div>

      <form @submit.prevent="handleSubmit">
        <div class="grid grid-cols-2 gap-4">
          <div class="card">
            <h3 class="section-title mb-4">Data Inisiator</h3>
            
            <div class="form-group">
              <label>Nama Lengkap Inisiator</label>
              <input type="text" class="form-control" v-model="form.nama_inisiator" placeholder="Nama Lengkap Inisiator" required />
            </div>

            <div class="form-group">
              <label>Jenis Inisiator</label>
              <select class="form-control" v-model="form.id_jenis_inisiator" @change="onJenisInisiatorChange" required>
                <option value="">Pilih Jenis Inisiator</option>
                <option v-for="jenis in jenisInisiatorOptions" :key="jenis.id" :value="jenis.id">
                  {{ jenis.nama_jenis_inisiator }}
                </option>
              </select>
            </div>

            <div class="form-group" v-if="selectedJenisName === 'Masyarakat'">
              <label>Kategori Masyarakat</label>
              <select class="form-control" v-model="form.id_masyarakat" required>
                <option value="">Pilih Kategori Masyarakat</option>
                <option v-for="masyarakat in masyarakatOptions" :key="masyarakat.id_masyarakat" :value="masyarakat.id_masyarakat">
                  {{ masyarakat.nama_masyarakat }}
                </option>
              </select>
            </div>

            <div class="form-group" v-if="selectedJenisName === 'OPD'">
              <label>Pilih OPD</label>
              <select class="form-control" v-model="form.id_opd" required>
                <option value="">Pilih OPD</option>
                <option v-for="opd in opdOptions" :key="opd.id" :value="opd.id">
                  {{ opd.nama_opd }}
                </option>
              </select>
            </div>

            <div class="form-group" v-if="selectedJenisName === 'Pemerintah'">
              <label>Instansi Pemerintah</label>
              <select class="form-control" v-model="form.id_pemerintah" required>
                <option value="">Pilih Instansi Pemerintah</option>
                <option v-for="pemerintah in pemerintahOptions" :key="pemerintah.id_pemerintah" :value="pemerintah.id_pemerintah">
                  {{ pemerintah.nama_pemerintah }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label>Kontak (Email / No. HP)</label>
              <input type="text" class="form-control" v-model="form.kontak" placeholder="Email atau Nomor HP" required />
            </div>
            
            <div class="form-group">
              <label>Kecamatan</label>
              <select class="form-control" v-model="form.id_kecamatan" @change="onKecamatanChange" required>
                <option value="">Pilih Kecamatan</option>
                <option v-for="kec in kecamatanOptions" :key="kec.id" :value="kec.id">
                  {{ kec.nama_kecamatan }}
                </option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Kelurahan</label>
              <select class="form-control" v-model="form.id_kelurahan" required :disabled="!form.id_kecamatan">
                <option value="">Pilih Kelurahan</option>
                <option v-for="kel in filteredKelurahans" :key="kel.id" :value="kel.id">
                  {{ kel.nama_kelurahan }}
                </option>
              </select>
            </div>
          </div>

          <div class="card">
            <h3 class="section-title mb-4">Data Inovasi</h3>
            <div class="form-group">
              <label>Nama Inovasi</label>
              <input type="text" class="form-control" v-model="form.nama_inovasi" placeholder="Nama Inovasi Anda" required />
            </div>
            <div class="form-group">
              <label>Tahapan Inovasi</label>
              <select class="form-control" v-model="form.id_tahapan" required disabled>
                <option value="">Pilih Tahapan</option>
                <option v-for="tahap in tahapanOptions" :key="tahap.id" :value="tahap.id">
                  {{ tahap.nama_tahapan }}
                </option>
              </select>
              <small class="text-muted" style="display: block; margin-top: 0.25rem; font-size: 0.75rem;">
                Tahapan inovasi ditentukan dan diperbarui oleh admin/verifikator.
              </small>
            </div>
            <div class="form-group">
              <label>Bentuk Inovasi</label>
              <select class="form-control" v-model="form.id_bentuk" required>
                <option value="">Pilih Bentuk</option>
                <option v-for="bentuk in bentukOptions" :key="bentuk.id" :value="bentuk.id">
                  {{ bentuk.nama_bentuk }}
                </option>
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
          <div class="form-group">
            <label>Link Marketplace / Referensi <span class="optional-badge">(Opsional)</span></label>
            <input type="url" class="form-control" v-model="form.link_marketplace" placeholder="https://..." />
          </div>
        </div>

        <div class="card mt-4">
          <h3 class="section-title mb-4">Media Dokumentasi <span class="optional-badge">(Maksimal 10 File)</span></h3>
          <div 
          class="upload-card" 
          :class="{ dragging: isDragging }"
          @dragover.prevent="onDragOver" 
          @dragleave.prevent="onDragLeave"
          @drop.prevent="onDrop"
          @click="triggerFileSelect"
          >
          <input type="file" ref="fileInput" @change="onFileChange" accept=".jpg,.jpeg,.png,.pdf" multiple style="display: none" />
          <div class="upload-content">
            <div class="upload-icon-circle">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cloud-icon">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="17 8 12 3 7 8" />
                <line x1="12" y1="3" x2="12" y2="15" />
              </svg>
            </div>
            <p class="upload-title">Klik untuk unggah atau seret file ke sini</p>
            <p class="upload-subtitle">Bisa pilih sampai 10 file sekaligus (JPG, PNG, PDF, Maks 10MB/file)</p>
          </div>
        </div>

  <div v-if="selectedFiles.length > 0" class="mt-3" style="display: flex; flex-direction: column; gap: 0.5rem;">
    <div v-for="(file, index) in selectedFiles" :key="index" class="file-preview-card">
      <div class="file-info">
        <span class="file-icon"><i class='bx bx-file'></i></span>
        <div>
          <p class="file-name">{{ file.name }}</p>
          <p class="file-size">{{ formatFileSize(file.size) }}</p>
        </div>
      </div>
      <button type="button" @click="removeSelectedFile(index)" class="btn-clear">✕</button>
    </div>
  </div>

  <div v-if="existingFiles.length > 0" class="mt-3">
    <p class="text-muted mb-2">File terunggah sebelumnya:</p>
    <div v-for="(fileUrl, index) in existingFiles" :key="index" class="file-preview-card mb-2">
      <div class="file-info">
        <span class="file-icon"><i class='bx bx-file'></i></span>
        <div>
          <a :href="fileUrl" target="_blank" class="existing-link">Lihat Dokumen {{ index + 1 }}</a>
        </div>
      </div>
      <button type="button" @click="removeExistingFile(index)" class="btn-clear">✕</button>
    </div>
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

const opdOptions = ref([])
const bentukOptions = ref([])
const tahapanOptions = ref([])
const jenisInisiatorOptions = ref([])
const kecamatanOptions = ref([])
const allKelurahans = ref([])
const masyarakatOptions = ref([]) // Master data masyarakat
const pemerintahOptions = ref([]) // Master data pemerintah

// Ubah ref file tunggal menjadi array
const selectedFiles = ref([]) // Menampung file baru
const existingFiles = ref([]) // Menampung URL file lama dari database
const fileInput = ref(null)
const isDragging = ref(false)

const form = ref({
  nama_inisiator: '',
  id_jenis_inisiator: '',
  kontak: '',
  id_kecamatan: '',
  id_kelurahan: '',
  id_pemerintah: '',
  id_masyarakat: '',
  id_opd: '',
  id_bentuk: '',
  id_tahapan: '',
  nama_inovasi: '',
  deskripsi: '',
  tahun_inovasi: new Date().getFullYear(),
  is_digital: false,
  link_marketplace: ''
})

// Mencari nama dari Jenis Inisiator yang sedang dipilih
const selectedJenisName = computed(() => {
  if (!form.value.id_jenis_inisiator) return ''
  const found = jenisInisiatorOptions.value.find(j => j.id === Number(form.value.id_jenis_inisiator))
  return found ? found.nama_jenis_inisiator : ''
})

// Reset sub-pilihan jika jenis inisiator berubah
function onJenisInisiatorChange() {
  form.value.id_masyarakat = ''
  form.value.id_opd = ''
  form.value.id_pemerintah = ''
}

const filteredKelurahans = computed(() => {
  if (!form.value.id_kecamatan) return []
  return allKelurahans.value.filter(k => k.id_kecamatan === Number(form.value.id_kecamatan))
})

function onKecamatanChange() {
  form.value.id_kelurahan = ''
}

// Uploader methods
function triggerFileSelect() {
  if (fileInput.value) {
    fileInput.value.click() // Membuka jendela pilih file bawaan windows/browser
  }
}

function onDragOver() {
  isDragging.value = true
}

function onDragLeave() {
  isDragging.value = false
}

function clearFile() {
  selectedFiles.value = []
  existingFiles.value = []
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

// Fungsi untuk memvalidasi dan memasukkan file ke array
function handleFilesUpdate(files) {
  const fileList = Array.from(files)
  
  // Cek validasi total file (maksimal 10)
  if (selectedFiles.value.length + fileList.length > 10) {
    alert("Maksimal file yang boleh diunggah adalah 10 file!")
    return
  }
  
  selectedFiles.value.push(...fileList)
}

function onFileChange(e) {
  if (e.target.files.length > 0) {
    handleFilesUpdate(e.target.files)
  }
}

function onDrop(e) {
  isDragging.value = false
  if (e.dataTransfer.files.length > 0) {
    handleFilesUpdate(e.dataTransfer.files)
  }
}

// Menghapus file baru pilihan user dari list sebelum di-submit
function removeSelectedFile(index) {
  selectedFiles.value.splice(index, 1)
}

// Menghapus file lama (untuk mode edit)
function removeExistingFile(index) {
  existingFiles.value.splice(index, 1)
}

async function handleSubmit() {
  submitting.value = true
  msg.value = ''
  
  try {
    const formData = new FormData()

    // 🎯 1. TRICK METHOD SPOOFING: Jika mode edit, paksa FormData mengirim sinyal 'PUT'
    if (isEdit.value) {
      formData.append('_method', 'PUT')
    }
    
    // Masukkan data seperti biasa
    formData.append('nama_inisiator', form.value.nama_inisiator)
    formData.append('id_jenis_inisiator', form.value.id_jenis_inisiator)
    formData.append('kontak', form.value.kontak)
    formData.append('id_kecamatan', form.value.id_kecamatan)
    formData.append('id_kelurahan', form.value.id_kelurahan)
    formData.append('id_bentuk', form.value.id_bentuk)
    formData.append('id_tahapan', form.value.id_tahapan)
    formData.append('nama_inovasi', form.value.nama_inovasi)
    formData.append('deskripsi', form.value.deskripsi || '')
    formData.append('tahun_inovasi', form.value.tahun_inovasi)
    formData.append('is_digital', form.value.is_digital ? '1' : '0')

    if (form.value.id_opd) formData.append('id_opd', form.value.id_opd)
    if (form.value.id_masyarakat) formData.append('id_masyarakat', form.value.id_masyarakat)
    if (form.value.id_pemerintah) formData.append('id_pemerintah', form.value.id_pemerintah)
    
    if (form.value.link_marketplace) {
      formData.append('link_marketplace', form.value.link_marketplace)
    }
    
    if (selectedFiles.value.length > 0) {
      selectedFiles.value.forEach((file, index) => {
        formData.append(`file_dokumentasi[${index}]`, file)
      })
    }

    // 🎯 2. PROSES TEMBAK API YANG SUDAH DISATUKAN
    if (isEdit.value) {
      formData.append('existing_files', JSON.stringify(existingFiles.value))
      
      // Menggunakan api.post tetapi membawa muatan '_method' = 'PUT' di atas tadi
      await api.post(`/inisiator/products/${route.query.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      msg.value = 'Inovasi berhasil diperbarui!'
    } else {
      // Jalur pengajuan data baru murni POST biasa
      await api.post('/inisiator/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
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
  handleSubmit()
}

onMounted(async () => {
  let profile = null
  try {
    const resMeta = await api.get('/inisiator/metadata')
    opdOptions.value = resMeta.data.opds || []
    bentukOptions.value = resMeta.data.bentuk_inovasis || []
    tahapanOptions.value = resMeta.data.tahapan_inovasis || []
    jenisInisiatorOptions.value = resMeta.data.jenis_inisiators || []
    kecamatanOptions.value = resMeta.data.kecamatans || []
    allKelurahans.value = resMeta.data.kelurahan || resMeta.data.kelurahans || []
    masyarakatOptions.value = resMeta.data.masyarakats || [] 
    pemerintahOptions.value = resMeta.data.pemerintahs || [] 
    
    profile = resMeta.data.inisiator_profile
    
    if (!isEdit.value) {
      if (profile) {
        form.value.nama_inisiator = profile.nama_inisiator || ''
        form.value.kontak = profile.kontak || ''
      }
      if (tahapanOptions.value.length > 0) {
        form.value.id_tahapan = tahapanOptions.value[0].id
      }
    }
  } catch (e) {
    console.error('Gagal mengambil metadata:', e)
  }

    if (isEdit.value) {
    try {
      const res = await api.get(`/inisiator/products/${route.query.id}`)
      const data = res.data
      
      form.value = {
        ...data,
        is_digital: !!data.is_digital,
        id_kecamatan: '',
        
        // 🎯 1. AUTOFILL DATA INISIATOR (Ambil dari objek relasi)
        nama_inisiator: data.inisiator_profile?.nama_inisiator || '',
        id_jenis_inisiator: data.inisiator_profile?.id_jenis_inisiator || '',
        
        // 🎯 2. AUTOFILL PENENTUAN KATEGORI
        // Sesuaikan nama variabel di bawah dengan v-model yang kamu pakai di template HTML-mu
        kategori_pilihan: data.id_opd ? 'opd' : (data.id_pemerintah ? 'pemerintah' : 'masyarakat'),
        id_opd: data.id_opd || '',
        id_masyarakat: data.id_masyarakat || '',
        id_pemerintah: data.id_pemerintah || ''
      }
      
      // Logika pencarian kecamatan berdasarkan kelurahan (Sudah Bagus)
      if (data.id_kelurahan && allKelurahans.value.length > 0) {
        const kel = allKelurahans.value.find(k => k.id === data.id_kelurahan)
        if (kel) {
          form.value.id_kecamatan = kel.id_kecamatan
        }
      }

      const linkMedia = data.media_inovasi?.find(m => m.jenis_media === 'link')
      if (linkMedia) {
        form.value.link_marketplace = linkMedia.isi_konten
      }
      
      if (data.media_inovasi && data.media_inovasi.length > 0) {
        existingFiles.value = data.media_inovasi
          .filter(m => m.jenis_media === 'file')
          .map(m => m.isi_konten)
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

.optional-badge {
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 500;
  margin-left: 0.5rem;
}
</style>
