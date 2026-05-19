<template>
  <div class="auth-wrapper">
    <div class="auth-card fade-in">
      <h2>Daftar Akun</h2>
      <p class="subtitle">Buat Akun untuk Mengakses Sistem</p>

      <div v-if="error" class="alert alert-error mb-4">{{ error }}</div>
      <div v-if="success" class="alert alert-success mb-4">{{ success }}</div>

      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="username">Username</label>
          <input id="username" type="text" class="form-control" v-model="username" placeholder="Masukkan Username" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" type="password" class="form-control" v-model="password" placeholder="Masukkan Password" required />
        </div>
        <div class="form-group">
          <label for="confirmPassword">Konfirmasi Password</label>
          <input id="confirmPassword" type="password" class="form-control" v-model="confirmPassword" placeholder="Konfirmasi Password Anda" required />
        </div>
        
        <div class="form-group">
          <label class="checkbox-container">
            <input type="checkbox" v-model="agree" required>
            <span class="checkmark"></span>
            Saya Setuju dengan Syarat dan Ketentuan
          </label>
        </div>

        <button type="submit" class="btn btn-primary w-full" :disabled="submitting">
          {{ submitting ? 'MEMPROSES...' : 'DAFTAR' }}
        </button>

        <p class="text-center mt-4 text-muted">
          Sudah Punya Akun? <router-link to="/login" class="forgot-link">Masuk</router-link>
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

const username = ref('')
const password = ref('')
const confirmPassword = ref('')
const agree = ref(false)
const error = ref('')
const success = ref('')
const submitting = ref(false)

async function handleRegister() {
  if (password.value !== confirmPassword.value) {
    error.value = 'Password konfirmasi tidak cocok'
    return
  }

  if (!agree.value) {
    error.value = 'Anda harus setuju dengan syarat dan ketentuan'
    return
  }

  error.value = ''
  success.value = ''
  submitting.value = true
  
  try {
    await auth.register({
      username: username.value,
      password: password.value,
      role: 'inisiator'
    })
    success.value = 'Registrasi berhasil! Mengalihkan ke dashboard...'
    setTimeout(() => {
      router.push('/inisiator')
    }, 1500)
  } catch (e) {
    error.value = e.response?.data?.message || 'Registrasi gagal. Silakan coba lagi.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.checkbox-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.875rem;
}

.forgot-link {
  color: var(--primary);
  font-weight: 500;
}

.alert-error {
  background-color: #fee2e2;
  color: #991b1b;
  padding: 0.75rem 1rem;
  border-radius: var(--border-radius);
  font-size: 0.875rem;
}

.alert-success {
  background-color: #dcfce7;
  color: #166534;
  padding: 0.75rem 1rem;
  border-radius: var(--border-radius);
  font-size: 0.875rem;
}
</style>
