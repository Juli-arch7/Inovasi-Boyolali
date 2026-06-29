<template>
  <div class="min-h-screen flex bg-gradient-to-br from-slate-900 via-purple-950 to-slate-900 relative overflow-hidden">

    <!-- Background Blobs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-blob"></div>
      <div class="absolute top-1/2 -left-32 w-80 h-80 bg-primary/15 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
      <div class="absolute -bottom-32 right-1/3 w-72 h-72 bg-cyan-500/15 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <!-- Grid overlay -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.02%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40 pointer-events-none"></div>

    <!-- Left: Branding Side -->
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 relative">
      <div>
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-12">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-primary flex items-center justify-center shadow-lg shadow-purple-500/30">
            <Lightbulb class="w-5 h-5 text-white" />
          </div>
          <span class="text-white font-extrabold text-xl tracking-tight">Portal Inovasi<span class="text-purple-400"> Boyolali</span></span>
        </div>

        <!-- Tagline -->
        <div class="mt-16">
          <h1 class="text-4xl font-black text-white leading-tight mb-4">
            Bergabung dan<br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">Ajukan Inovasi!</span>
          </h1>
          <p class="text-slate-300/80 text-lg leading-relaxed max-w-sm">
            Daftarkan diri sebagai inisiator untuk mulai mengajukan produk inovasi Anda kepada pemerintah daerah.
          </p>
        </div>

        <!-- Steps -->
        <div class="mt-12 space-y-4">
          <div v-for="(step, i) in steps" :key="i" class="flex items-center gap-4">
            <div class="w-8 h-8 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center flex-shrink-0">
              <span class="text-xs font-black text-purple-300">{{ i + 1 }}</span>
            </div>
            <p class="text-sm text-slate-300 font-medium">{{ step }}</p>
          </div>
        </div>
      </div>

      <p class="text-xs text-slate-500">© 2025 Portal Inovasi Boyolali. All rights reserved.</p>
    </div>

    <!-- Right: Register Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 relative">
      <div class="w-full max-w-md">

        <!-- Mobile Logo -->
        <div class="flex lg:hidden items-center gap-2.5 mb-8 justify-center">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-primary flex items-center justify-center">
            <Lightbulb class="w-5 h-5 text-white" />
          </div>
          <span class="text-white font-extrabold text-lg">Portal Inovasi <span class="text-purple-400">Boyolali</span></span>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 sm:p-10 shadow-2xl">
          <div class="mb-8">
            <h2 class="text-2xl font-extrabold text-white">Buat Akun Baru ✨</h2>
            <p class="text-slate-300/70 text-sm mt-1">Daftar sebagai inisiator inovasi</p>
          </div>

          <!-- Error Alert -->
          <div v-if="error" class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-rose-500/20 border border-rose-500/30">
            <AlertCircle class="w-4 h-4 text-rose-400 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-rose-300 font-medium">{{ error }}</p>
          </div>

          <!-- Success Alert -->
          <div v-if="success" class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30">
            <CheckCircle2 class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-emerald-300 font-medium">{{ success }}</p>
          </div>

          <form @submit.prevent="handleRegister" class="space-y-4">
            <!-- Nama Lengkap Field -->
            <div>
              <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap</label>
              <div class="relative">
                <UserCircle class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  id="name"
                  type="text"
                  v-model="name"
                  placeholder="Masukkan nama lengkap Anda"
                  required
                  class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/60 transition-all"
                />
              </div>
            </div>

            <!-- Email Field -->
            <div>
              <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Alamat Email</label>
              <div class="relative">
                <Mail class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  id="email"
                  type="email"
                  v-model="email"
                  placeholder="Masukkan alamat email Anda"
                  required
                  class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/60 transition-all"
                />
              </div>
            </div>

            <!-- Username Field -->
            <div>
              <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Username</label>
              <div class="relative">
                <User class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  id="username"
                  type="text"
                  v-model="username"
                  placeholder="Buat username unik"
                  required
                  class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/60 transition-all"
                />
              </div>
            </div>

            <!-- Password Field -->
            <div>
              <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Password</label>
              <div class="relative">
                <Lock class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  id="password"
                  :type="showPassword ? 'text' : 'password'"
                  v-model="password"
                  placeholder="Buat password yang kuat"
                  required
                  class="w-full pl-11 pr-12 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/60 transition-all"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors cursor-pointer"
                >
                  <EyeOff v-if="showPassword" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Confirm Password Field -->
            <div>
              <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Konfirmasi Password</label>
              <div class="relative">
                <Lock class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  id="confirmPassword"
                  :type="showPassword ? 'text' : 'password'"
                  v-model="confirmPassword"
                  placeholder="Ulangi password Anda"
                  required
                  class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/60 transition-all"
                  :class="confirmPassword && password !== confirmPassword ? 'border-rose-500/60 focus:ring-rose-500/40' : ''"
                />
              </div>
              <p v-if="confirmPassword && password !== confirmPassword" class="text-xs text-rose-400 font-medium mt-1">
                Password tidak cocok
              </p>
            </div>

            <!-- Terms & Conditions -->
            <div>
              <label class="flex items-start gap-3 cursor-pointer">
                <div
                  @click="agree = !agree"
                  class="w-4 h-4 rounded border-2 flex items-center justify-center transition-all cursor-pointer flex-shrink-0 mt-0.5"
                  :class="agree ? 'bg-purple-500 border-purple-500' : 'border-white/30 bg-transparent'"
                >
                  <Check v-if="agree" class="w-2.5 h-2.5 text-white" />
                </div>
                <span class="text-sm text-slate-300 leading-relaxed select-none">
                  Saya setuju dengan <span class="text-purple-400 font-semibold">Syarat & Ketentuan</span> dan <span class="text-purple-400 font-semibold">Kebijakan Privasi</span>
                </span>
              </label>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="submitting"
              class="w-full py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-purple-500 to-primary hover:from-purple-600 hover:to-blue-700 shadow-lg shadow-purple-500/30 transition-all disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 cursor-pointer mt-2"
            >
              <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
              {{ submitting ? 'Memproses...' : 'Buat Akun Sekarang' }}
            </button>
          </form>

          <p class="text-center text-sm text-slate-400 mt-6">
            Sudah punya akun?
            <router-link to="/login" class="text-purple-400 font-bold hover:underline ml-1">Masuk</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { Lightbulb, User, UserCircle, Mail, Lock, Eye, EyeOff, Check, AlertCircle,
  CheckCircle2, Loader2
} from 'lucide-vue-next'

const auth = useAuthStore()
const router = useRouter()

const name = ref('')
const email = ref('')
const username = ref('')
const password = ref('')
const confirmPassword = ref('')
const agree = ref(false)
const error = ref('')
const success = ref('')
const submitting = ref(false)
const showPassword = ref(false)

const steps = [
  'Daftarkan akun sebagai inisiator',
  'Lengkapi profil dan data institusi',
  'Ajukan inovasi melalui formulir online',
  'Tunggu proses verifikasi dari admin',
]

async function handleRegister() {
  if (password.value !== confirmPassword.value) {
    error.value = 'Password konfirmasi tidak cocok'
    return
  }
  if (!agree.value) {
    error.value = 'Anda harus menyetujui syarat dan ketentuan'
    return
  }

  error.value = ''
  success.value = ''
  submitting.value = true

  try {
    await auth.register({
      name: name.value,
      email: email.value,
      username: username.value,
      password: password.value,
      role: 'inisiator'
    })
    success.value = 'Registrasi berhasil! Mengalihkan ke dashboard...'
    setTimeout(() => router.push('/inisiator'), 1500)
  } catch (e) {
    error.value = e.response?.data?.message || 'Registrasi gagal. Silakan coba lagi.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
@keyframes blob {
  0% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0, 0) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
