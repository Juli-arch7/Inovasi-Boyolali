<template>
  <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-slate-900 border-r border-slate-200/50 dark:border-slate-800/50 h-[calc(100vh-4rem)] sticky top-16 z-30 transition-colors duration-300">
    <!-- Active Status Info -->
    <div class="p-6 border-b border-slate-100 dark:border-slate-800/50">
      <div class="flex items-center gap-3">
        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
        <div class="flex flex-col">
          <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Sesi Aktif</span>
          <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ roleName }}</span>
        </div>
      </div>
    </div>
    
    <!-- Navigation List -->
    <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
      <router-link
        v-for="item in menuItems"
        :key="item.path"
        :to="item.path"
        class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100 transition-all group"
        active-class="bg-blue-50/80 text-primary! dark:bg-blue-950/20 dark:text-blue-400!"
      >
        <component :is="item.icon" class="w-5 h-5 text-slate-400 dark:text-slate-500 group-hover:text-primary dark:group-hover:text-blue-450 transition-colors" />
        <span>{{ item.label }}</span>
      </router-link>
    </nav>
    
    <!-- Footer Logout Button -->
    <div class="p-4 border-t border-slate-100 dark:border-slate-800/50">
      <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-rose-600 dark:text-rose-450 hover:bg-rose-50 dark:hover:bg-rose-950/25 transition-all hover:scale-[1.02] active:scale-[0.98]">
        <LogOut class="w-5 h-5" />
        <span>Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { useToastStore } from '../stores/toast'
import {
  LayoutDashboard,
  ShieldCheck,
  Package,
  Users,
  UserCheck,
  Lightbulb,
  PlusCircle,
  LogOut,
  History
} from 'lucide-vue-next'

const auth = useAuthStore()
const toastStore = useToastStore()
const router = useRouter()

const roleName = computed(() => {
  switch (auth.userRole) {
    case 'superadmin': return 'Super Admin'
    case 'admin': return 'Admin'
    case 'inisiator': return 'Inisiator'
    default: return 'User'
  }
})

const menuItems = computed(() => {
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

async function handleLogout() {
  try {
    await auth.logout()
    toastStore.show('Anda berhasil keluar dari sistem.', 'success')
    router.push('/')
  } catch (e) {
    toastStore.show('Gagal keluar sistem.', 'error')
  }
}
</script>
