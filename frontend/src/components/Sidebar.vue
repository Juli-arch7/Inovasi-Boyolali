<template>
  <aside class="sidebar">
    <div class="sidebar-header mb-4">
      <span class="user-role">{{ roleName }}</span>
    </div>
    <nav class="sidebar-nav">
      <router-link v-for="item in menuItems" :key="item.path" :to="item.path" class="sidebar-link">
        <i :class="item.icon + ' nav-icon'"></i>
        <span class="link-text">{{ item.label }}</span>
      </router-link>
    </nav>
    <div class="sidebar-footer mt-auto">
      <button class="btn btn-outline w-full logout-btn" @click="handleLogout">
        <i class="bx bx-log-out nav-icon"></i> <span class="link-text">Logout</span>
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
      { path: '/inisiator', label: 'Inovasi Saya', icon: 'bx bx-bulb' },
      { path: '/inisiator/pengajuan', label: 'Pengajuan Inovasi', icon: 'bx bx-edit' },
    ]
  } else if (auth.userRole === 'superadmin') {
    return [
      { path: '/superadmin', label: 'Admin', icon: 'bx bx-shield-quarter' },
      { path: '/admin/users', label: 'Pengguna', icon: 'bx bx-user' },
      { path: '/admin', label: 'Dashboard', icon: 'bx bx-grid-alt' },
      { path: '/admin/verifikasi', label: 'Verifikasi', icon: 'bx bx-check-shield' },
      { path: '/admin/products', label: 'Produk Inovasi', icon: 'bx bx-box' },
    ]
  } else if (auth.userRole === 'admin') {
    return [
      { path: '/admin', label: 'Dashboard', icon: 'bx bx-grid-alt' },
      { path: '/admin/verifikasi', label: 'Verifikasi', icon: 'bx bx-check-shield' },
      { path: '/admin/products', label: 'Produk Inovasi', icon: 'bx bx-box' },
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
.sidebar {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.sidebar-header {
  padding: 1rem 1rem 0;
}

.user-role {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-light);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-left: 0.5rem;
}

.sidebar-footer {
  padding: 1rem;
}

.nav-icon {
  font-size: 1.25rem;
  margin-right: 0.25rem;
  line-height: 1;
}

.link-text {
  font-weight: 500;
  font-size: 0.95rem;
}

.logout-btn {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0.75rem 1rem;
  border-radius: 12px;
  color: var(--text-muted);
  border: none;
  background: transparent;
  transition: all 0.3s ease;
  font-weight: 500;
}

.logout-btn:hover {
  background: #fee2e2;
  color: #ef4444;
}
</style>
