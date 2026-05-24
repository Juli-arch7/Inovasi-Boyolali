<template>
  <div id="app">
    <header class="navbar">
      <router-link to="/" class="logo-container">
        <!-- Elegant government/civic building SVG icon -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="logo-icon">
          <path d="M12 2L1 7h22L12 2zm10 7H2v2h20V9zM4 12h3v7H4v-7zm6 0h3v7h-3v-7zm6 0h3v7h-3v-7zm-4 8h10v2H2v-2h10z"/>
        </svg>
        <span class="logo-text">BAPPERIDA BOYOLALI</span>
      </router-link>
      <nav>
        <router-link to="/" exact-active-class="active-link">Beranda</router-link>
        <router-link to="/" active-class="active-link">Inovasi</router-link>
      </nav>
      <div class="nav-actions">
        <template v-if="auth.isAuthenticated">
          <span class="text-muted mr-2" style="font-size: 0.9rem; font-weight: 500;">Halo, <strong>{{ auth.user?.name || 'User' }}</strong></span>
          <router-link v-if="auth.userRole === 'superadmin'" to="/superadmin" class="btn btn-outline btn-sm">Super Admin</router-link>
          <router-link v-if="auth.userRole === 'admin' || auth.userRole === 'superadmin'" to="/admin" class="btn btn-outline btn-sm">Admin</router-link>
          <router-link v-if="auth.userRole === 'inisiator'" to="/inisiator" class="btn btn-outline btn-sm">Dashboard</router-link>
          <button class="btn btn-primary btn-sm" @click="handleLogout">Logout</button>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn-primary btn-sm" style="border-radius: 8px; padding: 0.6rem 1.75rem;">Masuk</router-link>
        </template>
      </div>
    </header>
    <router-view />
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

async function handleLogout() {
  await auth.logout()
  router.push('/')
}
</script>

<style scoped>
.mr-2 {
  margin-right: 0.75rem;
}
</style>
