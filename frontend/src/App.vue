<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col transition-colors duration-300">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 w-full glass shadow-sm border-b border-slate-200/50 dark:border-slate-800/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        
        <!-- Logo -->
        <router-link to="/" class="flex items-center gap-2.5 group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary to-secondary flex items-center justify-center text-white shadow-md shadow-primary/20 group-hover:scale-105 transition-transform duration-300">
            <Landmark class="w-5 h-5" />
          </div>
          <div class="flex flex-col">
            <span class="font-extrabold text-sm tracking-wider bg-gradient-to-r from-primary via-accent to-secondary bg-clip-text text-transparent">BAPPERIDA</span>
            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tracking-widest uppercase -mt-0.5">BOYOLALI</span>
          </div>
        </router-link>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-8">
          <router-link
            to="/inovasi"
            class="text-sm font-medium transition-colors py-1.5 border-b-2 border-transparent"
            :class="navLinkClass(isPublicActive)"
          >
            Inovasi
          </router-link>
          <template v-if="auth.isAuthenticated">
            <router-link
              v-if="auth.userRole === 'superadmin'"
              to="/superadmin"
              class="text-sm font-medium transition-colors py-1.5 border-b-2 border-transparent"
              :class="navLinkClass(isSuperAdminActive)"
            >
              Super Admin
            </router-link>
            <router-link
              v-if="auth.userRole === 'admin'"
              to="/admin"
              class="text-sm font-medium transition-colors py-1.5 border-b-2 border-transparent"
              :class="navLinkClass(isAdminActive)"
            >
              Admin Panel
            </router-link>
            <router-link
              v-if="auth.userRole === 'inisiator'"
              to="/inisiator"
              class="text-sm font-medium transition-colors py-1.5 border-b-2 border-transparent"
              :class="navLinkClass(isInisiatorActive)"
            >
              Dashboard Inisiator
            </router-link>
          </template>
        </nav>

        <!-- Action Items (Dark Mode Toggle & Auth) -->
        <div class="hidden md:flex items-center gap-4">
          <!-- Dark Mode Button -->
          <button @click="toggleDarkMode" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border border-slate-200/50 dark:border-slate-800/50" aria-label="Toggle Dark Mode">
            <Sun v-if="isDark" class="w-5 h-5 text-amber-400" />
            <Moon v-else class="w-5 h-5 text-slate-600" />
          </button>

          <!-- Auth Button -->
          <template v-if="auth.isAuthenticated">
            <button @click="handleLogout" class="px-4 h-10 text-sm font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-950/60 rounded-xl border border-rose-200/50 dark:border-rose-900/30 flex items-center gap-2 transition-all hover:scale-[1.02] active:scale-[0.98]">
              <LogOut class="w-4 h-4" />
              Keluar
            </button>
          </template>
          <router-link v-else to="/login" class="px-5 h-10 text-sm font-semibold text-white bg-gradient-to-r from-primary to-accent hover:from-primary-hover hover:to-accent-hover rounded-xl flex items-center justify-center shadow-md shadow-primary/20 hover:shadow-lg hover:shadow-primary/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
            Masuk
          </router-link>
        </div>

        <!-- Mobile Menu Controls -->
        <div class="flex items-center gap-3 md:hidden">
          <!-- Dark Mode Button (Mobile) -->
          <button @click="toggleDarkMode" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/50 dark:border-slate-800/50 transition-colors" aria-label="Toggle Dark Mode">
            <Sun v-if="isDark" class="w-5 h-5 text-amber-400" />
            <Moon v-else class="w-5 h-5 text-slate-600" />
          </button>
          
          <!-- Hamburger -->
          <button @click="mobileMenuOpen = !mobileMenuOpen" class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/50 dark:border-slate-800/50 transition-colors" aria-label="Open Menu">
            <X v-if="mobileMenuOpen" class="w-5 h-5" />
            <Menu v-else class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Mobile Dropdown Menu -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
      >
        <div v-if="mobileMenuOpen" class="md:hidden border-t border-slate-200/50 dark:border-slate-800/50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-lg px-4 py-4 space-y-3 shadow-lg">
          <router-link
            to="/inovasi"
            @click="mobileMenuOpen = false"
            class="block px-4 py-2.5 rounded-xl text-base font-semibold transition-colors"
            :class="mobileNavLinkClass(isPublicActive)"
          >
            Inovasi
          </router-link>
          <template v-if="auth.isAuthenticated">
            <!-- Role-based Dashboard Links for Mobile Menu -->
            <div class="border-t border-slate-200/50 dark:border-slate-800/50 my-2"></div>
            <div class="px-4 py-1 text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Menu Panel</div>
            
            <router-link
              v-for="item in mobileMenuItems"
              :key="item.path"
              :to="item.path"
              @click="mobileMenuOpen = false"
              v-slot="{ isActive, isExactActive }"
            >
              <div
                class="block px-4 py-2.5 rounded-xl text-base font-semibold transition-colors flex items-center gap-3"
                :class="(item.path === '/admin' || item.path === '/inisiator') 
                  ? (isExactActive ? 'bg-blue-50/50 text-primary dark:bg-blue-950/20 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary')
                  : (isActive ? 'bg-blue-50/50 text-primary dark:bg-blue-950/20 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary')"
              >
                <component :is="item.icon" class="w-5 h-5 text-slate-450 dark:text-slate-505" />
                <span>{{ item.label }}</span>
              </div>
            </router-link>

            <div class="border-t border-slate-200/50 dark:border-slate-800/50 my-2"></div>
            <button @click="handleLogoutMobile" class="w-full text-left px-4 py-2.5 rounded-xl text-base font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 flex items-center gap-2">
              <LogOut class="w-4 h-4" />
              Keluar
            </button>
          </template>
          <router-link v-else to="/login" @click="mobileMenuOpen = false" class="block text-center w-full px-4 py-3 rounded-xl text-base font-bold text-white bg-gradient-to-r from-primary to-accent">
            Masuk
          </router-link>
        </div>
      </transition>
    </header>

    <!-- Content Router Wrapper -->
    <main class="flex-1 flex flex-col">
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Global Toast Notifications Container -->
    <div class="fixed bottom-6 right-6 z-9999 flex flex-col gap-3 max-w-md w-full px-4 pointer-events-none">
      <transition-group
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-for="toast in toastStore.toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-center p-4 rounded-2xl shadow-xl border glass animate-slide-in"
          :class="{
            'border-emerald-200/50 dark:border-emerald-900/30 text-emerald-800 dark:text-emerald-300': toast.type === 'success',
            'border-rose-200/50 dark:border-rose-900/30 text-rose-800 dark:text-rose-300': toast.type === 'error',
            'border-amber-200/50 dark:border-amber-900/30 text-amber-800 dark:text-amber-300': toast.type === 'warning',
            'border-sky-200/50 dark:border-sky-900/30 text-sky-800 dark:text-sky-300': toast.type === 'info',
          }"
        >
          <!-- Status Icon -->
          <div class="flex-shrink-0 mr-3">
            <CheckCircle2 v-if="toast.type === 'success'" class="w-5 h-5 text-emerald-500" />
            <XCircle v-else-if="toast.type === 'error'" class="w-5 h-5 text-rose-500" />
            <AlertTriangle v-else-if="toast.type === 'warning'" class="w-5 h-5 text-amber-500" />
            <Info v-else class="w-5 h-5 text-sky-500" />
          </div>
          <!-- Message -->
          <div class="text-sm font-semibold flex-1">{{ toast.message }}</div>
          <!-- Close Button -->
          <button @click="toastStore.remove(toast.id)" class="ml-4 p-1 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 rounded-lg transition-colors pointer-events-auto">
            <X class="w-3.5 h-3.5 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200" />
          </button>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'
import { useToastStore } from './stores/toast'
import {
  Sun,
  Moon,
  Menu,
  X,
  Landmark,
  LogOut,
  CheckCircle2,
  XCircle,
  AlertTriangle,
  Info,
  LayoutDashboard,
  ShieldCheck,
  Package,
  Users,
  UserCheck,
  Lightbulb,
  PlusCircle,
  History
} from 'lucide-vue-next'

const auth = useAuthStore()
const toastStore = useToastStore()
const router = useRouter()
const route = useRoute()

const isDark = ref(false)
const mobileMenuOpen = ref(false)

const isPublicActive = computed(() => route.path === '/inovasi')
const isSuperAdminActive = computed(() => route.path.startsWith('/superadmin') || route.path.startsWith('/admin'))
const isAdminActive = computed(() => route.path.startsWith('/admin'))
const isInisiatorActive = computed(() => route.path.startsWith('/inisiator'))

const mobileMenuItems = computed(() => {
  if (auth.userRole === 'inisiator') {
    return [
      { path: '/inisiator', label: 'Inovasi Saya', icon: Lightbulb },
      { path: '/inisiator/pengajuan', label: 'Pengajuan Inovasi', icon: PlusCircle },
    ]
  } else if (auth.userRole === 'superadmin') {
    return [
      { path: '/admin', label: 'Dashboard', icon: LayoutDashboard },
      { path: '/superadmin', label: 'Kelola Admin', icon: UserCheck },
      { path: '/admin/users', label: 'Pengguna', icon: Users },
      { path: '/admin/verifikasi', label: 'Verifikasi', icon: ShieldCheck },
      { path: '/admin/products', label: 'Produk Inovasi', icon: Package },
      { path: '/admin/logs', label: 'Log Aktivitas', icon: History },
    ]
  } else if (auth.userRole === 'admin') {
    return [
      { path: '/admin', label: 'Dashboard', icon: LayoutDashboard },
      { path: '/admin/verifikasi', label: 'Verifikasi', icon: ShieldCheck },
      { path: '/admin/products', label: 'Produk Inovasi', icon: Package },
      { path: '/admin/logs', label: 'Log Aktivitas', icon: History },
    ]
  }
  return []
})

function navLinkClass(isActive) {
  return isActive
    ? 'text-primary dark:text-primary border-primary'
    : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary'
}

function mobileNavLinkClass(isActive) {
  return isActive
    ? 'bg-blue-50/50 text-primary dark:bg-blue-950/20'
    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary'
}

function toggleDarkMode() {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

async function handleLogout() {
  try {
    await auth.logout()
    toastStore.show('Anda berhasil keluar dari sistem.', 'success')
    router.push('/')
  } catch (e) {
    toastStore.show('Gagal keluar sistem.', 'error')
  }
}

async function handleLogoutMobile() {
  mobileMenuOpen.value = false
  await handleLogout()
}

onMounted(() => {
  // Initialize dark mode
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  } else {
    isDark.value = false
    document.documentElement.classList.remove('dark')
  }
})
</script>

<style scoped>
/* Page transition styles */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
