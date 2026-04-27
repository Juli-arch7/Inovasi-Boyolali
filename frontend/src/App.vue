<template>
  <div id="app">
    <header class="navbar">
      <router-link to="/" class="logo">🚀 Inovasi Daerah</router-link>
      <nav>
        <router-link to="/">Beranda</router-link>
        <template v-if="auth.isAuthenticated">
          <router-link v-if="auth.userRole === 'superadmin'" to="/superadmin">Super Admin</router-link>
          <router-link v-if="auth.userRole === 'admin' || auth.userRole === 'superadmin'" to="/admin">Admin</router-link>
          <router-link v-if="auth.userRole === 'inisiator'" to="/inisiator">Dashboard</router-link>
          <button class="btn btn-outline btn-sm" @click="handleLogout">Logout</button>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn-outline btn-sm">Masuk</router-link>
          <router-link to="/register" class="btn btn-primary btn-sm">Daftar Inisiator</router-link>
        </template>
      </nav>
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
