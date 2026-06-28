<script setup>
import { onMounted, computed } from 'vue'
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import '@/assets/styles/cms.css'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

onMounted(auth.init)

const pageTitle = computed(() => {
  if (route.name === 'cms-dashboard') return 'Dashboard'
  if (route.name === 'cms-artikel-nieuw') return 'Nieuw artikel'
  if (route.name === 'cms-artikel-edit') return 'Artikel bewerken'
  if (route.name === 'cms-layout-editor') return 'Panorama-layout'
  return 'CMS'
})

async function uitloggen() {
  await auth.logout()
  router.push({ name: 'cms-login' })
}
</script>

<template>
  <div class="cms-app">
    <aside class="cms-sidebar">
      <div class="cms-sidebar__brand">
        <img src="/img/Logo_ingeklapt.png" alt="HUA" />
        <div>
          <h1>HUA CMS</h1>
          <span>Het Utrechts Archief</span>
        </div>
      </div>

      <nav class="cms-nav">
        <RouterLink class="cms-nav__link" :to="{ name: 'cms-dashboard' }">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9 12 3l9 6v11a2 2 0 0 1-2 2h-4v-7H10v7H6a2 2 0 0 1-2-2z" />
          </svg>
          Dashboard
        </RouterLink>

        <RouterLink class="cms-nav__link" :to="{ name: 'cms-artikel-nieuw' }">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Nieuw artikel
        </RouterLink>

        <RouterLink class="cms-nav__link" :to="{ name: 'cms-layout-editor' }">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="6" width="6" height="12" />
            <rect x="11" y="4" width="6" height="16" />
            <rect x="19" y="9" width="2" height="9" />
          </svg>
          Panorama-layout
        </RouterLink>

        <div class="cms-nav__divider"></div>

        <a class="cms-nav__link" href="/" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
            <polyline points="15 3 21 3 21 9" />
            <line x1="10" y1="14" x2="21" y2="3" />
          </svg>
          Bekijk site
        </a>

        <button type="button" class="cms-nav__link" @click="uitloggen" style="background:transparent;border:none;text-align:left;cursor:pointer;font-family:inherit;font-size:0.95rem;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          Uitloggen
        </button>
      </nav>

      <div class="cms-sidebar__footer">
        Vue 3 + Vite — schoolproject 6.1
      </div>
    </aside>

    <div class="cms-main">
      <div class="cms-topbar">
        <h2 class="cms-topbar__title">{{ pageTitle }}</h2>
        <div class="cms-topbar__user">
          <span :class="['cms-topbar__mode', auth.mode === 'supabase' ? 'cms-topbar__mode--supabase' : 'cms-topbar__mode--dev']">
            {{ auth.mode === 'supabase' ? 'Supabase' : 'Dev modus' }}
          </span>
          <span>{{ auth.user?.email }}</span>
        </div>
      </div>

      <div class="cms-content">
        <RouterView />
      </div>
    </div>
  </div>
</template>
