import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('auth_token') || '')

  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role || '')

  async function login(email, password) {
    const res = await api.post('/login', { email, password })
    token.value = res.data.token
    user.value = res.data.user
    localStorage.setItem('auth_token', token.value)
    localStorage.setItem('user', JSON.stringify(user.value))
    return res.data
  }

  async function register(data) {
    const res = await api.post('/register', data)
    token.value = res.data.token
    user.value = res.data.user
    localStorage.setItem('auth_token', token.value)
    localStorage.setItem('user', JSON.stringify(user.value))
    return res.data
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch (e) { /* ignore */ }
    token.value = ''
    user.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
  }

  return { user, token, isAuthenticated, userRole, login, register, logout }
})
