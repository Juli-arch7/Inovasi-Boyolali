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
  { path: '/', name: 'Home', component: Home },
  { path: '/products/:id', name: 'ProductDetail', component: ProductDetail },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  {
    path: '/superadmin',
    name: 'SuperAdmin',
    component: SuperAdminDashboard,
    meta: { requiresAuth: true, role: 'superadmin' }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/verifikasi',
    name: 'AdminVerificationList',
    component: VerificationList,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/verifikasi/:id',
    name: 'AdminVerificationDetail',
    component: VerificationDetail,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/inisiator',
    name: 'Inisiator',
    component: InisiatorDashboard,
    meta: { requiresAuth: true, role: 'inisiator' }
  },
  {
    path: '/inisiator/pengajuan',
    name: 'InisiatorInnovationForm',
    component: InnovationForm,
    meta: { requiresAuth: true, role: 'inisiator' }
  },
  {
    path: '/inisiator/products/:id',
    name: 'InisiatorProductDetail',
    component: ProductDetail,
    meta: { requiresAuth: true, role: 'inisiator' }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
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
