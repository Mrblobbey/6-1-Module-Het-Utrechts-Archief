import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/cms/login',
      name: 'cms-login',
      component: () => import('@/views/cms/CmsLogin.vue'),
      meta: { layout: 'blank' },
    },
    {
      path: '/cms',
      component: () => import('@/components/cms/CmsLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'cms-dashboard',
          component: () => import('@/views/cms/CmsDashboard.vue'),
        },
        {
          path: 'artikel/nieuw',
          name: 'cms-artikel-nieuw',
          component: () => import('@/views/cms/CmsArtikelEdit.vue'),
        },
        {
          path: 'artikel/:id',
          name: 'cms-artikel-edit',
          component: () => import('@/views/cms/CmsArtikelEdit.vue'),
          props: true,
        },
        {
          path: 'layout',
          name: 'cms-layout-editor',
          component: () => import('@/views/cms/CmsPanoramaLayout.vue'),
        },
      ],
    },
  ],
})

router.beforeEach(async (to) => {
  if (!to.meta.requiresAuth) return true
  const auth = useAuthStore()
  if (auth.user === null) await auth.init()
  if (!auth.isLoggedIn) {
    return { name: 'cms-login', query: { redirect: to.fullPath } }
  }
  return true
})

export default router
