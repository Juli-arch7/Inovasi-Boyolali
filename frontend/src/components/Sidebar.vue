<template>
  <aside class="sidebar">
    <nav class="sidebar-nav">
      <router-link 
        v-for="item in menuItems" 
        :key="item.path" 
        :to="item.path" 
        class="sidebar-link"
        active-class="active-link"
      >
        <span class="icon">{{ item.icon }}</span>
        <span class="label">{{ item.label }}</span>
      </router-link>
      
      <!-- Inactive Setting Menu as shown in mockup -->
      <a href="#" class="sidebar-link disabled-link">
        <span class="icon">⚙️</span>
        <span class="label">Setting</span>
      </a>
    </nav>
    
    <div class="sidebar-footer mt-auto">
      <button class="btn-logout-sidebar" @click="handleLogout">
        <!-- Logout icon -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="logout-icon-svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        Logout
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

const menuItems = computed(() => {
  if (auth.userRole === 'inisiator') {
    return [
      { path: '/inisiator', label: 'My Innovations', icon: '💡' },
      { path: '/inisiator/pengajuan', label: 'Pengajuan Inovasi', icon: '➕' },
    ]
  } else if (auth.userRole === 'admin' || auth.userRole === 'superadmin') {
    const items = [
      { path: '/admin', label: 'Dashboard', icon: '📊' },
      { path: '/admin/verifikasi', label: 'Verifikasi', icon: '✅' },
      { path: '/admin/products', label: 'Produk Inovasi', icon: '📦' },
      { path: '/admin/users', label: 'Pengguna', icon: '👥' },
    ]
    if (auth.userRole === 'superadmin') {
      items.push({ path: '/superadmin', label: 'Manajemen Admin', icon: '🛠️' })
    }
    return items
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
  width: 260px;
  background: #ffffff;
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  padding: 2rem 0;
  flex-shrink: 0;
}

.sidebar-nav {
  padding: 0 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.85rem 1.25rem;
  color: var(--text-secondary);
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  transition: var(--transition);
  text-decoration: none;
}

.sidebar-link:hover {
  background: #f8fafc;
  color: var(--primary);
}

.sidebar-link.active-link,
.sidebar-link.router-link-exact-active {
  background: rgba(37, 99, 235, 0.06);
  color: var(--primary);
  position: relative;
}

.sidebar-link.active-link::before,
.sidebar-link.router-link-exact-active::before {
  content: "";
  position: absolute;
  left: 0;
  top: 15%;
  height: 70%;
  width: 4px;
  background: var(--primary);
  border-radius: 0 4px 4px 0;
}

.sidebar-link .icon {
  font-size: 1.25rem;
}

.disabled-link {
  opacity: 0.75;
  cursor: default;
}
.disabled-link:hover {
  background: transparent;
  color: var(--text-secondary);
}

.sidebar-footer {
  padding: 1.5rem 1.5rem 0 1.5rem;
  border-top: 1px solid var(--border);
  margin-top: auto;
}

.btn-logout-sidebar {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  background: transparent;
  border: none;
  color: var(--text-secondary);
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  padding: 0.5rem 0;
  transition: var(--transition);
}

.btn-logout-sidebar:hover {
  color: var(--danger);
}

.logout-icon-svg {
  width: 20px;
  height: 20px;
  color: currentColor;
}
</style>
