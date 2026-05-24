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
import AdminProducts from '../views/admin/Products.vue'
import AdminUsers from '../views/admin/Users.vue'
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
    path: '/admin/products',
    name: 'AdminProducts',
    component: AdminProducts,
    meta: { requiresAuth: true, role: 'admin' }
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsers,
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
    name: 'InnovationForm',
    component: InnovationForm,
    meta: { requiresAuth: true, role: 'inisiator' }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'Login' })
  }

  if (to.meta.role) {
    // Allow superadmin to access admin routes too
    const allowed = to.meta.role === 'admin'
      ? ['admin', 'superadmin'].includes(auth.userRole)
      : auth.userRole === to.meta.role
    if (!allowed) {
      return next({ name: 'Home' })
    }
  }

  next()
})

export default router
