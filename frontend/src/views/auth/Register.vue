<template>
  <div class="auth-container">
    <div class="auth-card fade-in" style="max-width: 500px">
      <h2>Daftar Sebagai Inisiator</h2>
      <p class="subtitle">Buat akun untuk mulai mengajukan produk inovasi daerah Anda</p>

      <div v-if="error" class="alert alert-error">{{ error }}</div>

      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="name">Nama Lengkap</label>
          <input id="name" type="text" class="form-control" v-model="form.name" placeholder="Joko Widodo" required />
        </div>
        
        <div class="form-group">
          <label for="username">Username</label>
          <input id="username" type="text" class="form-control" v-model="form.username" placeholder="jokowi" required />
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" type="email" class="form-control" v-model="form.email" placeholder="joko@contoh.com" required />
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" type="password" class="form-control" v-model="form.password" placeholder="••••••••" required minlength="6" />
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:0.5rem" :disabled="submitting">
          {{ submitting ? 'Memproses...' : 'Daftar' }}
        </button>

        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
          Sudah punya akun? <router-link to="/login" style="font-weight: 500;">Masuk di sini</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const form = ref({
  name: '',
  username: '',
  email: '',
  password: '',
  role: 'inisiator'
})

const error = ref('')
const submitting = ref(false)

async function handleRegister() {
  error.value = ''
  submitting.value = true
  try {
    const data = await auth.register(form.value)
    
    // After registration, redirect to dashboard
    if (data.user.role === 'inisiator') {
      router.push('/inisiator')
    } else {
      router.push('/')
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Registrasi gagal. Silakan coba lagi.'
  } finally {
    submitting.value = false
  }
}
</script>
