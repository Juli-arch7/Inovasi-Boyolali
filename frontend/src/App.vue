<template>
  <div class="app-container">
    <header class="navbar">
      <router-link to="/" class="logo">
        <span class="logo-icon">🏛️</span>
        BAPPERIDA BOYOLALI
      </router-link>
      <nav>
        <router-link to="/">Beranda</router-link>
        <router-link to="/inisiator">Inovasi</router-link>
        <template v-if="auth.isAuthenticated">
          <router-link v-if="auth.userRole === 'superadmin'" to="/superadmin">Super Admin</router-link>
          <router-link v-if="auth.userRole === 'admin' || auth.userRole === 'superadmin'" to="/admin">Admin</router-link>
          <router-link v-if="auth.userRole === 'inisiator'" to="/inisiator">Dashboard</router-link>
          <button class="btn btn-outline btn-sm" @click="handleLogout">Logout</button>
        </template>
        <router-link v-else to="/login" class="btn btn-primary btn-sm">Masuk</router-link>
      </nav>
    </header>
    <main class="main-content">
      <router-view />
    </main>
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
