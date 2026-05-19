<template>
  <div class="auth-wrapper">
    <div class="auth-card fade-in">
      <h2>Masuk Sistem Inovasi</h2>
      <p class="subtitle">Masuk untuk Mengakses Sistem Inovasi</p>

      <div v-if="error" class="alert alert-error mb-4">{{ error }}</div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Email/Username</label>
          <div class="input-with-icon">
            <span class="icon">👤</span>
            <input id="email" type="text" class="form-control" v-model="email" placeholder="Masukkan Email atau Username" required />
          </div>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-with-icon">
            <span class="icon">🔒</span>
            <input id="password" :type="showPassword ? 'text' : 'password'" class="form-control" v-model="password" placeholder="Masukkan Password" required />
            <span class="icon-eye" @click="showPassword = !showPassword">👁️</span>
          </div>
        </div>
        
        <div class="form-footer mb-4">
          <label class="checkbox-container">
            <input type="checkbox" v-model="rememberMe">
            <span class="checkmark"></span>
            Ingat Saya
          </label>
          <router-link to="/forgot-password" class="forgot-link">Lupa Password?</router-link>
        </div>

        <button type="submit" class="btn btn-primary w-full" :disabled="submitting">
          {{ submitting ? 'MEMPROSES...' : 'MASUK' }}
        </button>

        <p class="text-center mt-4 text-muted">
          Belum Punya Akun? <router-link to="/register" class="forgot-link">Daftar</router-link>
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

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const showPassword = ref(false)
const error = ref('')
const submitting = ref(false)

async function handleLogin() {
  error.value = ''
  submitting.value = true
  try {
    const data = await auth.login(email.value, password.value)
    const role = data.user.role
    
    // Debug: If you can see this in console, it means login succeeded
    console.log('Logged in as:', role)

    if (role === 'superadmin' || role === 'admin') {
      await router.push('/admin')
    } else if (role === 'inisiator') {
      await router.push('/inisiator')
    } else {
      await router.push('/')
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Login gagal. Periksa kembali kredensial Anda.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.input-with-icon {
  position: relative;
}

.input-with-icon .icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-light);
}

.input-with-icon .form-control {
  padding-left: 40px;
}

.icon-eye {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: var(--text-light);
}

.form-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
}

.checkbox-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
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
</style>
