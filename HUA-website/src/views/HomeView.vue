<script setup>
import { onMounted, onUnmounted, computed, ref } from 'vue'
import PanoramaViewer from '@/components/panorama/PanoramaViewer.vue'
import { useArtikelenStore } from '@/stores/artikelen'
import { supabaseEnabled } from '@/composables/useSupabase'

const store = useArtikelenStore()
const artikelen = computed(() => store.sorted)
const justRefreshed = ref(false)

const devMode = !supabaseEnabled

function onVisibilityChange() {
  if (!document.hidden) store.laden()
}

async function ververs() {
  await store.laden()
  justRefreshed.value = true
  setTimeout(() => (justRefreshed.value = false), 1200)
}

onMounted(() => {
  store.laden()
  document.addEventListener('visibilitychange', onVisibilityChange)
  window.addEventListener('focus', store.laden)
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', onVisibilityChange)
  window.removeEventListener('focus', store.laden)
})
</script>

<template>
  <section v-if="store.loading && artikelen.length === 0" class="status">Laden…</section>
  <section v-else-if="store.error" class="status status-error">
    Fout bij laden: {{ store.error }}
  </section>
  <PanoramaViewer v-else :artikelen="artikelen" />

  <button
    v-if="devMode"
    class="dev-refresh"
    type="button"
    :class="{ 'dev-refresh--ok': justRefreshed }"
    @click="ververs"
    title="Ververs panorama uit localStorage (CMS-wijzigingen ophalen)"
  >
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="23 4 23 10 17 10" />
      <polyline points="1 20 1 14 7 14" />
      <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15" />
    </svg>
    <span>{{ justRefreshed ? 'Ververst!' : 'Ververs panorama' }}</span>
  </button>
</template>

<style scoped>
.status {
  padding: 4rem 2rem;
  text-align: center;
  font-size: 1.1rem;
}
.status-error {
  color: var(--red);
}

.dev-refresh {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #166a5a;
  color: #fff;
  border: none;
  padding: 0.6rem 1rem;
  border-radius: 30px;
  font-family: inherit;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
  z-index: 9000;
  transition: background 0.2s ease, transform 0.1s ease;
}

.dev-refresh:hover {
  background: #0f4f43;
}

.dev-refresh:active {
  transform: translateY(1px);
}

.dev-refresh--ok {
  background: var(--red);
}

.dev-refresh svg {
  width: 18px;
  height: 18px;
}
</style>
