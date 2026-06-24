<template>
  <div class="min-h-screen flex bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 relative overflow-hidden">

    <!-- Background Blobs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -left-40 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-blob"></div>
      <div class="absolute top-1/2 -right-32 w-80 h-80 bg-cyan-500/15 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
      <div class="absolute -bottom-32 left-1/3 w-72 h-72 bg-purple-500/15 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <!-- Grid pattern overlay -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.02%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40 pointer-events-none"></div>

    <!-- Left: Branding Side -->
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 relative">
      <div>
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-12">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-cyan-500 flex items-center justify-center shadow-lg shadow-primary/30">
            <Lightbulb class="w-5 h-5 text-white" />
          </div>
          <span class="text-white font-extrabold text-xl tracking-tight">Portal Inovasi<span class="text-cyan-400"> Boyolali</span></span>
        </div>

        <!-- Tagline -->
        <div class="mt-16">
          <h1 class="text-4xl font-black text-white leading-tight mb-4">
            Platform Inovasi<br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Pemerintah Daerah</span>
          </h1>
          <p class="text-slate-300/80 text-lg leading-relaxed max-w-sm">
            Kelola, pantau, dan verifikasi seluruh produk inovasi di Kabupaten Boyolali secara digital.
          </p>
        </div>

        <!-- Feature badges -->
        <div class="mt-12 flex flex-wrap gap-3">
          <div v-for="feat in features" :key="feat.label" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10">
            <component :is="feat.icon" class="w-4 h-4 text-cyan-400" />
            <span class="text-xs font-semibold text-slate-200">{{ feat.label }}</span>
          </div>
        </div>
      </div>

      <!-- Stats at bottom -->
      <div class="grid grid-cols-3 gap-4">
        <div v-for="stat in stats" :key="stat.label" class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4 text-center">
          <p class="text-2xl font-black text-white">{{ stat.value }}</p>
          <p class="text-xs text-slate-400 font-medium mt-0.5">{{ stat.label }}</p>
        </div>
      </div>
    </div>

    <!-- Right: Login Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 relative">
      <div class="w-full max-w-md">

        <!-- Mobile Logo -->
        <div class="flex lg:hidden items-center gap-2.5 mb-8 justify-center">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-cyan-500 flex items-center justify-center">
            <Lightbulb class="w-5 h-5 text-white" />
          </div>
          <span class="text-white font-extrabold text-lg">Portal Inovasi <span class="text-cyan-400">Boyolali</span></span>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 sm:p-10 shadow-2xl">
          <div class="mb-8">
            <h2 class="text-2xl font-extrabold text-white">Selamat Datang 👋</h2>
            <p class="text-slate-300/70 text-sm mt-1">Masuk untuk mengakses sistem inovasi</p>
          </div>

          <!-- Error Alert -->
          <div v-if="error" class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-rose-500/20 border border-rose-500/30">
            <AlertCircle class="w-4 h-4 text-rose-400 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-rose-300 font-medium">{{ error }}</p>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <!-- Email/Username Field -->
            <div>
              <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Email / Username</label>
              <div class="relative">
                <User class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  id="email"
                  type="text"
                  v-model="email"
                  placeholder="Masukkan email atau username"
                  required
                  class="w-full pl-11 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500/60 transition-all"
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
                  placeholder="Masukkan password"
                  required
                  class="w-full pl-11 pr-12 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500/60 transition-all"
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

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
              <label class="flex items-center gap-2 cursor-pointer">
                <div
                  @click="rememberMe = !rememberMe"
                  class="w-4 h-4 rounded border-2 flex items-center justify-center transition-all cursor-pointer"
                  :class="rememberMe ? 'bg-primary border-primary' : 'border-white/30 bg-transparent'"
                >
                  <Check v-if="rememberMe" class="w-2.5 h-2.5 text-white" />
                </div>
                <span class="text-sm text-slate-300 font-medium select-none">Ingat Saya</span>
              </label>
              <router-link to="/forgot-password" class="text-xs text-cyan-400 font-semibold hover:underline">
                Lupa Password?
              </router-link>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="submitting"
              class="w-full py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-primary to-cyan-500 hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-primary/30 transition-all disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 cursor-pointer"
            >
              <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
              {{ submitting ? 'Memproses...' : 'Masuk Sekarang' }}
            </button>
          </form>

          <p class="text-center text-sm text-slate-400 mt-6">
            Belum punya akun?
            <router-link to="/register" class="text-cyan-400 font-bold hover:underline ml-1">Daftar Gratis</router-link>
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
import {
  Lightbulb, User, Lock, Eye, EyeOff, Check, AlertCircle,
  Loader2, ShieldCheck, BarChart3, Globe
} from 'lucide-vue-next'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const showPassword = ref(false)
const error = ref('')
const submitting = ref(false)

const features = [
  { icon: ShieldCheck, label: 'Sistem Terenkripsi' },
  { icon: BarChart3, label: 'Dashboard Real-time' },
  { icon: Globe, label: 'Portal Publik' },
]

const stats = [
  { value: '100+', label: 'Inovasi' },
  { value: '50+', label: 'Inisiator' },
  { value: '15+', label: 'OPD' },
]

async function handleLogin() {
  error.value = ''
  submitting.value = true
  try {
    const data = await auth.login(email.value, password.value)
    const role = data.user.role
    if (role === 'superadmin' || role === 'admin') {
      await router.push('/admin')
    } else if (role === 'inisiator') {
      await router.push('/inisiator')
    } else {
      await router.push('/')
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Login gagal. Periksa kembali kredensial Anda.'
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
