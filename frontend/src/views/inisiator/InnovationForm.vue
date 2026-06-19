<template>
  <div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950/40 transition-colors duration-300">
    <Sidebar />

    <main class="flex-1 p-6 sm:p-8 overflow-y-auto max-w-5xl mx-auto w-full">

      <!-- Page Header -->
      <div class="pb-6 mb-8 border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="flex items-center gap-3">
          <button
            @click="$router.push('/inisiator')"
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer"
          >
            <ArrowLeft class="w-4 h-4" /> Kembali
          </button>
          <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
              {{ isEdit ? 'Edit Inovasi' : 'Ajukan Inovasi Baru' }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
              Lengkapi formulir di bawah untuk mengajukan inovasi Anda.
            </p>
          </div>
        </div>
      </div>

      <!-- Alert Messages -->
      <Transition name="fade-slide">
        <div v-if="msg" class="mb-6 flex items-start gap-3 px-5 py-4 rounded-2xl" :class="isError ? 'bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40' : 'bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/40'">
          <component :is="isError ? AlertCircle : CheckCircle2" class="w-5 h-5 flex-shrink-0 mt-0.5" :class="isError ? 'text-rose-500' : 'text-emerald-500'" />
          <p class="text-sm font-medium" :class="isError ? 'text-rose-700 dark:text-rose-400' : 'text-emerald-700 dark:text-emerald-400'">{{ msg }}</p>
        </div>
      </Transition>

      <form @submit.prevent="handleSubmit" class="space-y-6">

        <!-- Step 1: Data Inisiator -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
              <span class="text-xs font-black text-primary">1</span>
            </div>
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Data Inisiator</h2>
          </div>
          <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            <div class="form-field">
              <label class="field-label">Nama Lengkap Inisiator <span class="text-rose-500">*</span></label>
              <input type="text" v-model="form.nama_inisiator" placeholder="Nama lengkap inisiator" required class="field-input" />
            </div>

            <div class="form-field">
              <label class="field-label">Jenis Inisiator <span class="text-rose-500">*</span></label>
              <select v-model="form.id_jenis_inisiator" @change="onJenisChange" required class="field-input">
                <option value="">Pilih Jenis</option>
                <option v-for="jenis in jenisInisiatorOptions" :key="jenis.id" :value="jenis.id">
                  {{ jenis.nama_jenis_inisiator }}
                </option>
              </select>
            </div>

            <div class="form-field">
              <label class="field-label">Kontak (Email / No. HP) <span class="text-rose-500">*</span></label>
              <input type="text" v-model="form.kontak" placeholder="Email atau Nomor HP" required class="field-input" />
            </div>

            <!-- OPD: hanya tampil jika BUKAN masyarakat -->
            <div v-if="!isMasyarakat" class="form-field">
              <label class="field-label">OPD <span class="text-rose-500">*</span></label>
              <select v-model="form.id_opd" :required="!isMasyarakat" class="field-input">
                <option value="">Pilih OPD</option>
                <option v-for="opd in opdOptions" :key="opd.id_opd ?? opd.id" :value="opd.id_opd ?? opd.id">
                  {{ opd.nama_opd }}
                </option>
              </select>
            </div>

            <div class="form-field">
              <label class="field-label">Kecamatan <span class="text-rose-500">*</span></label>
              <select v-model="form.id_kecamatan" @change="onKecamatanChange" required class="field-input">
                <option value="">Pilih Kecamatan</option>
                <option v-for="kec in kecamatanOptions" :key="kec.id" :value="kec.id">
                  {{ kec.nama_kecamatan }}
                </option>
              </select>
            </div>

            <div class="form-field">
              <label class="field-label">Kelurahan <span class="text-rose-500">*</span></label>
              <select v-model="form.id_kelurahan" required :disabled="!form.id_kecamatan" class="field-input disabled:opacity-50 disabled:cursor-not-allowed">
                <option value="">Pilih Kelurahan</option>
                <option v-for="kel in filteredKelurahans" :key="kel.id" :value="kel.id">
                  {{ kel.nama_kelurahan }}
                </option>
              </select>
            </div>

          </div>
        </div>

        <!-- Step 2: Data Inovasi -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
              <span class="text-xs font-black text-purple-500">2</span>
            </div>
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Data Inovasi</h2>
          </div>
          <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            <div class="form-field md:col-span-2">
              <label class="field-label">Nama Inovasi <span class="text-rose-500">*</span></label>
              <input type="text" v-model="form.nama_inovasi" placeholder="Nama inovasi Anda" required class="field-input" />
            </div>

            <div class="form-field">
              <label class="field-label">Tahapan Inovasi <span class="text-rose-500">*</span></label>
              <select v-model="form.id_tahapan" required class="field-input">
                <option value="">Pilih Tahapan</option>
                <option v-for="tahap in tahapanOptions" :key="tahap.id" :value="tahap.id">
                  {{ tahap.nama_tahapan }}
                </option>
              </select>
            </div>

            <div class="form-field">
              <label class="field-label">Bentuk Inovasi <span class="text-rose-500">*</span></label>
              <select v-model="form.id_bentuk" required class="field-input">
                <option value="">Pilih Bentuk</option>
                <option v-for="bentuk in bentukOptions" :key="bentuk.id" :value="bentuk.id">
                  {{ bentuk.nama_bentuk }}
                </option>
              </select>
            </div>

            <div class="form-field">
              <label class="field-label">Tahun Inovasi <span class="text-rose-500">*</span></label>
              <input type="number" v-model="form.tahun_inovasi" :min="2000" :max="2030" required class="field-input" />
            </div>

            <div class="form-field flex items-center pt-6">
              <label class="flex items-center gap-3 cursor-pointer">
                <div
                  @click="form.is_digital = !form.is_digital"
                  class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all cursor-pointer flex-shrink-0"
                  :class="form.is_digital ? 'bg-primary border-primary' : 'border-slate-300 dark:border-slate-600 bg-transparent'"
                >
                  <Check v-if="form.is_digital" class="w-3 h-3 text-white" />
                </div>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none">Inovasi Digital</span>
              </label>
            </div>

          </div>
        </div>

        <!-- Step 3: Deskripsi & Detail -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
              <span class="text-xs font-black text-emerald-500">3</span>
            </div>
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Deskripsi & Detail</h2>
          </div>
          <div class="p-6 space-y-5">

            <div class="form-field">
              <label class="field-label">Deskripsi Inovasi</label>
              <textarea
                v-model="form.deskripsi"
                rows="5"
                placeholder="Jelaskan inovasi Anda secara detail, termasuk latar belakang, tujuan, dan manfaatnya..."
                class="field-input resize-none"
              ></textarea>
            </div>

            <div class="form-field">
              <label class="field-label">
                Link Marketplace / Referensi
                <span class="ml-1.5 text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-400">Opsional</span>
              </label>
              <input type="url" v-model="form.link_marketplace" placeholder="https://..." class="field-input" />
            </div>

          </div>
        </div>

        <!-- Step 4: Media Dokumentasi -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                <span class="text-xs font-black text-amber-500">4</span>
              </div>
              <h2 class="text-base font-bold text-slate-800 dark:text-white">Media Dokumentasi</h2>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400">Opsional</span>
          </div>
          <div class="p-6">

            <!-- Upload Zone -->
            <div
              class="rounded-2xl border-2 border-dashed p-10 text-center cursor-pointer transition-all"
              :class="isDragging
                ? 'border-primary bg-primary/5 dark:bg-primary/10 -translate-y-1 shadow-md'
                : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/40 hover:border-primary/50 hover:bg-primary/5 dark:hover:bg-primary/5'"
              @dragover.prevent="onDragOver"
              @dragleave.prevent="onDragLeave"
              @drop.prevent="onDrop"
              @click="triggerFileSelect"
            >
              <input type="file" ref="fileInput" @change="onFileChange" accept=".jpg,.jpeg,.png,.pdf" style="display: none" />
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center">
                  <Upload class="w-7 h-7 text-primary" />
                </div>
                <div>
                  <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Klik untuk unggah atau seret file</p>
                  <p class="text-xs text-slate-400 mt-1">JPG, PNG, PDF — Maks. 10MB</p>
                </div>
              </div>
            </div>

            <!-- File Preview -->
            <div v-if="selectedFile || existingFile" class="mt-4 flex items-center justify-between bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                  <FileText class="w-5 h-5 text-primary" />
                </div>
                <div>
                  <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    {{ selectedFile ? selectedFile.name : 'Dokumen Terunggah' }}
                  </p>
                  <p v-if="selectedFile" class="text-xs text-slate-400 mt-0.5">{{ formatFileSize(selectedFile.size) }}</p>
                  <a v-else-if="existingFile" :href="existingFile" target="_blank" class="text-xs text-primary hover:underline font-semibold">
                    Lihat file saat ini
                  </a>
                </div>
              </div>
              <button type="button" @click="clearFile" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-950/30 transition-all cursor-pointer">
                <X class="w-4 h-4" />
              </button>
            </div>

          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 pb-8">
          <button
            type="button"
            @click="$router.push('/inisiator')"
            class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold rounded-xl text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer"
          >
            Batal
          </button>
          <div class="flex gap-3 w-full sm:w-auto">
            <button
              type="button"
              @click="saveDraft"
              :disabled="submitting"
              class="flex-1 sm:flex-initial px-5 py-2.5 text-sm font-bold rounded-xl text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2"
            >
              <Save class="w-4 h-4" />
              Simpan Draft
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="flex-1 sm:flex-initial px-6 py-2.5 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-primary to-blue-600 hover:from-blue-700 hover:to-blue-700 shadow-sm shadow-primary/30 transition-all disabled:opacity-60 cursor-pointer inline-flex items-center justify-center gap-2"
            >
              <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
              <Send v-else class="w-4 h-4" />
              {{ submitting ? 'Mengirim...' : 'Kirim Pengajuan' }}
            </button>
          </div>
        </div>

      </form>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '../../components/Sidebar.vue'
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../services/api'
import {
  ArrowLeft, Check, AlertCircle, CheckCircle2,
  Upload, FileText, X, Save, Send, Loader2
} from 'lucide-vue-next'

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

const fileInput = ref(null)
const selectedFile = ref(null)
const existingFile = ref(null)
const isDragging = ref(false)

const form = ref({
  nama_inisiator: '',
  id_jenis_inisiator: '',
  kontak: '',
  id_kecamatan: '',
  id_kelurahan: '',
  id_opd: '',
  id_bentuk: '',
  id_tahapan: '',
  nama_inovasi: '',
  deskripsi: '',
  tahun_inovasi: new Date().getFullYear(),
  is_digital: false,
  link_marketplace: ''
})

const filteredKelurahans = computed(() => {
  if (!form.value.id_kecamatan) return []
  return allKelurahans.value.filter(k => k.id_kecamatan === Number(form.value.id_kecamatan))
})

// Cek apakah jenis inisiator yang dipilih adalah "Masyarakat"
const isMasyarakat = computed(() => {
  if (!form.value.id_jenis_inisiator) return false
  const jenis = jenisInisiatorOptions.value.find(j => j.id === Number(form.value.id_jenis_inisiator) || j.id === form.value.id_jenis_inisiator)
  return jenis?.nama_jenis_inisiator?.toLowerCase().includes('masyarakat') ?? false
})

function onKecamatanChange() {
  form.value.id_kelurahan = ''
}

function onJenisChange() {
  // Reset id_opd jika beralih ke masyarakat
  if (isMasyarakat.value) {
    form.value.id_opd = ''
  }
}

function triggerFileSelect() {
  fileInput.value.click()
}

function onFileChange(e) {
  const files = e.target.files
  if (files && files[0]) selectedFile.value = files[0]
}

function onDragOver() { isDragging.value = true }
function onDragLeave() { isDragging.value = false }

function onDrop(e) {
  isDragging.value = false
  const files = e.dataTransfer.files
  if (files && files[0]) selectedFile.value = files[0]
}

function clearFile() {
  selectedFile.value = null
  existingFile.value = null
  if (fileInput.value) fileInput.value.value = ''
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

async function handleSubmit() {
  submitting.value = true
  msg.value = ''
  try {
    const formData = new FormData()
    formData.append('nama_inisiator', form.value.nama_inisiator)
    if (form.value.id_jenis_inisiator) formData.append('id_jenis_inisiator', form.value.id_jenis_inisiator)
    formData.append('kontak', form.value.kontak)
    if (form.value.id_kecamatan) formData.append('id_kecamatan', form.value.id_kecamatan)
    if (form.value.id_kelurahan) formData.append('id_kelurahan', form.value.id_kelurahan)
    if (form.value.id_opd) formData.append('id_opd', form.value.id_opd)
    if (form.value.id_masyarakat) formData.append('id_masyarakat', form.value.id_masyarakat)
    if (form.value.id_pemerintah) formData.append('id_pemerintah', form.value.id_pemerintah)
    if (form.value.id_bentuk) formData.append('id_bentuk', form.value.id_bentuk)
    if (form.value.id_tahapan) formData.append('id_tahapan', form.value.id_tahapan)
    formData.append('nama_inovasi', form.value.nama_inovasi)
    formData.append('deskripsi', form.value.deskripsi || '')
    formData.append('tahun_inovasi', form.value.tahun_inovasi)
    formData.append('is_digital', form.value.is_digital ? '1' : '0')
    if (form.value.link_marketplace) formData.append('link_marketplace', form.value.link_marketplace)
    if (selectedFile.value) formData.append('file_dokumentasi', selectedFile.value)

    if (isEdit.value) {
      formData.append('_method', 'PUT')
      await api.post(`/inisiator/products/${route.query.id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
      msg.value = 'Inovasi berhasil diperbarui!'
    } else {
      await api.post('/inisiator/products', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
      msg.value = 'Inovasi berhasil diajukan!'
    }
    isError.value = false
    setTimeout(() => router.push('/inisiator'), 1500)
  } catch (e) {
    msg.value = e.response?.data?.message || 'Gagal menyimpan inovasi. Pastikan seluruh kolom terisi.'
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
    profile = resMeta.data.inisiator_profile

    if (profile && !isEdit.value) {
      form.value.nama_inisiator = profile.nama_inisiator || ''
      form.value.kontak = profile.kontak || ''
      form.value.id_jenis_inisiator = profile.id_jenis_inisiator || ''
      form.value.id_kelurahan = profile.id_kelurahan || ''
      if (profile.id_kelurahan) {
        const kel = allKelurahans.value.find(k => k.id === profile.id_kelurahan)
        if (kel) form.value.id_kecamatan = kel.id_kecamatan
      }
    }
  } catch (e) {
    console.error('Gagal mengambil metadata:', e)
  }

  if (isEdit.value) {
    try {
      const res = await api.get(`/inisiator/products/${route.query.id}`)
      const data = res.data
      form.value = { ...data, is_digital: !!data.is_digital, id_kecamatan: '' }

      if (data.id_kelurahan && allKelurahans.value.length > 0) {
        const kel = allKelurahans.value.find(k => k.id === data.id_kelurahan)
        if (kel) form.value.id_kecamatan = kel.id_kecamatan
      }

      if (profile) {
        form.value.nama_inisiator = profile.nama_inisiator || ''
        form.value.kontak = profile.kontak || ''
        form.value.id_jenis_inisiator = profile.id_jenis_inisiator || ''
      }

      const linkMedia = data.media_inovasi?.find(m => m.jenis_media === 'link')
      if (linkMedia) form.value.link_marketplace = linkMedia.isi_konten

      const fileMedia = data.media_inovasi?.find(m => m.jenis_media === 'file')
      if (fileMedia) existingFile.value = fileMedia.isi_konten
    } catch (e) {
      console.error(e)
    }
  }
})
</script>

<style scoped>
.form-field {
  display: flex;
  flex-direction: column;
}

.field-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.5rem;
}

:is(.dark) .field-label {
  color: #94a3b8;
}

.field-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  background-color: #f8fafc;
  color: #0f172a;
  font-size: 0.875rem;
  transition: all 0.15s ease;
  outline: none;
}

.field-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  background-color: white;
}

:is(.dark) .field-input {
  background-color: rgba(15, 23, 42, 0.8);
  border-color: #334155;
  color: #e2e8f0;
}

:is(.dark) .field-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  background-color: #0f172a;
}

.field-input::placeholder {
  color: #94a3b8;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
