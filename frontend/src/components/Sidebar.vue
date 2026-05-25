<template>
  <aside class="sidebar">
    <div class="sidebar-header mb-4">
      <span class="user-role">{{ roleName }}</span>
    </div>
    <nav class="sidebar-nav">
      <router-link v-for="item in menuItems" :key="item.path" :to="item.path" class="sidebar-link">
        <span class="icon">{{ item.icon }}</span>
        {{ item.label }}
      </router-link>
    </nav>
    <div class="sidebar-footer mt-auto">
      <button class="btn btn-outline w-full" @click="handleLogout">
        <span class="icon">🚪</span> Logout
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
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
      { path: '/inisiator', label: 'Dashboard', icon: '📊' },
      { path: '/inisiator/innovations', label: 'Inovasi Saya', icon: '💡' },
      { path: '/inisiator/submissions', label: 'Pengajuan', icon: '📝' },
      { path: '/inisiator/settings', label: 'Settings', icon: '⚙️' },
    ]
  } else if (auth.userRole === 'admin' || auth.userRole === 'superadmin') {
    return [
      { path: '/admin', label: 'Dashboard', icon: '📊' },
      { path: '/admin/verifikasi', label: 'Verifikasi', icon: '✅' },
      { path: '/admin/products', label: 'Produk Inovasi', icon: '📦' },
      { path: '/admin/users', label: 'Pengguna', icon: '👥' },
    ]
  }
  return []
})

async function handleLogout() {
  await auth.logout()
  router.push('/')
}
</script>

<style scoped>
.sidebar-header {
  padding: 0 1rem;
}

.user-role {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-light);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid var(--border-color);
}
</style>
