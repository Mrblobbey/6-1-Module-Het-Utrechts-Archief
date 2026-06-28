<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import HeaderNav from '@/components/layout/HeaderNav.vue'
import SiteFooter from '@/components/layout/SiteFooter.vue'

const route = useRoute()
const isCmsRoute = computed(() => route.path.startsWith('/cms'))
</script>

<template>
  <template v-if="isCmsRoute">
    <RouterView />
  </template>
  <template v-else>
    <HeaderNav />
    <main>
      <RouterView />
    </main>
    <SiteFooter />
  </template>
</template>
