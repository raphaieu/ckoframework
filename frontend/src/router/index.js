import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('../views/Dashboard.vue')
  },
  {
    path: '/cashflow',
    name: 'Cashflow',
    component: () => import('../views/Cashflow.vue')
  },
  {
    path: '/trades/day',
    name: 'TradesDay',
    component: () => import('../views/TradesDay.vue')
  },
  {
    path: '/trades/forex',
    name: 'Forex',
    component: () => import('../views/Forex.vue')
  },
  {
    path: '/investments/swing',
    name: 'Swing',
    component: () => import('../views/Swing.vue')
  },
  {
    path: '/crypto',
    name: 'Crypto',
    component: () => import('../views/Crypto.vue')
  },
  {
    path: '/intake',
    name: 'Intake',
    component: () => import('../views/Intake.vue')
  },
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('../views/Settings.vue')
  },
  // Rotas legadas (manter para compatibilidade)
  {
    path: '/users',
    name: 'Users',
    component: () => import('../views/Users.vue')
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('../views/About.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router