import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Public
import Home from '../views/public/Home.vue'
import ProductDetail from '../views/public/ProductDetail.vue'

// Auth
import Login from '../views/auth/Login.vue'
import Register from '../views/auth/Register.vue'

// Dashboards
import SuperAdminDashboard from '../views/superadmin/Dashboard.vue'
import AdminDashboard from '../views/admin/Dashboard.vue'
import VerificationList from '../views/admin/VerificationList.vue'
import VerificationDetail from '../views/admin/VerificationDetail.vue'
import InisiatorDashboard from '../views/inisiator/Dashboard.vue'
import InnovationForm from '../views/inisiator/InnovationForm.vue'

const routes = [
  { path: '/', name: 'Home', component: Home, meta: { title: 'Beranda' } },
  { path: '/inovasi', name: 'Inovasi', component: Home, meta: { title: 'Inovasi' } },
  { path: '/products/:id', name: 'ProductDetail', component: ProductDetail, meta: { title: 'Detail Inovasi' } },
  { path: '/login', name: 'Login', component: Login, meta: { title: 'Login' } },
  { path: '/register', name: 'Register', component: Register, meta: { title: 'Register' } },
  {
    path: '/superadmin',
    name: 'SuperAdmin',
    component: SuperAdminDashboard,
    meta: { requiresAuth: true, role: 'superadmin', title: 'Dashboard Super Admin' }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin', title: 'Dashboard Admin' }
  },
  {
    path: '/admin/verifikasi',
    name: 'AdminVerificationList',
    component: VerificationList,
    meta: { requiresAuth: true, role: 'admin', title: 'Daftar Verifikasi' }
  },
  {
    path: '/admin/verifikasi/:id',
    name: 'AdminVerificationDetail',
    component: VerificationDetail,
    meta: { requiresAuth: true, role: 'admin', title: 'Detail Verifikasi' }
  },
  {
    path: '/inisiator',
    name: 'Inisiator',
    component: InisiatorDashboard,
    meta: { requiresAuth: true, role: 'inisiator', title: 'Dashboard Inisiator' }
  },
  {
    path: '/inisiator/pengajuan',
    name: 'InisiatorInnovationForm',
    component: InnovationForm,
    meta: { requiresAuth: true, role: 'inisiator', title: 'Pengajuan Inovasi' }
  },
  {
    path: '/inisiator/products/:id',
    name: 'InisiatorProductDetail',
    component: ProductDetail,
    meta: { requiresAuth: true, role: 'inisiator', title: 'Detail Inovasi' }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  // Update document title berdasarkan meta title
  document.title = to.meta.title ? `${to.meta.title} - Bapperida Boyolali` : 'Bapperida Boyolali'

  const auth = useAuthStore()
  const token = localStorage.getItem('auth_token')
  const userStr = localStorage.getItem('user')
  const user = userStr ? JSON.parse(userStr) : null
  const role = user?.role || ''

  // If page requires auth and no token, go to login
  if (to.meta.requiresAuth && !token) {
    return next({ name: 'Login' })
  }

  // If page has role requirement
  if (to.meta.role) {
    // If user is superadmin or admin, they can access any admin/superadmin route
    const isAdminPath = to.path.startsWith('/admin') || to.path.startsWith('/superadmin')
    const isUserAdmin = role === 'admin' || role === 'superadmin'

    if (isAdminPath && !isUserAdmin) {
      return next({ name: 'Home' })
    }

    // Specifically check for inisiator role
    if (to.meta.role === 'inisiator' && role !== 'inisiator') {
      return next({ name: 'Home' })
    }
  }

  next()
})

export default router
