<template>
  <div class="auth-container">
    <div class="auth-card fade-in">
      <h2>Selamat Datang</h2>
      <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>

      <div v-if="error" class="alert alert-error">{{ error }}</div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" type="email" class="form-control" v-model="email" placeholder="email@contoh.com" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" type="password" class="form-control" v-model="password" placeholder="••••••••" required />
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:0.5rem" :disabled="submitting">
          {{ submitting ? 'Memproses...' : 'Masuk' }}
        </button>
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

const email = ref('')
const password = ref('')
const error = ref('')
const submitting = ref(false)

async function handleLogin() {
  error.value = ''
  submitting.value = true
  try {
    const data = await auth.login(email.value, password.value)
    const role = data.user.role
    if (role === 'superadmin') router.push('/superadmin')
    else if (role === 'admin') router.push('/admin')
    else if (role === 'inisiator') router.push('/inisiator')
    else router.push('/')
  } catch (e) {
    error.value = e.response?.data?.message || 'Login gagal. Periksa kembali kredensial Anda.'
  } finally {
    submitting.value = false
  }
}
</script>
