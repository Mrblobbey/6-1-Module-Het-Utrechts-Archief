<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import Hotspot from './Hotspot.vue'
import PolygonHotspot from './PolygonHotspot.vue'
import InfoPanel from './InfoPanel.vue'
import Minimap from './Minimap.vue'
import IntroOverlay from './IntroOverlay.vue'
import PanoramaControls from './PanoramaControls.vue'
import { useIntroStore } from '@/stores/intro'

const props = defineProps({
  artikelen: { type: Array, required: true },
})

const intro = useIntroStore()
const containerRef = ref(null)
const scrollerRef = ref(null)

const activeArtikel = ref(null)
const waypointsVisible = ref(true)
const isPlaying = ref(false)
const currentZoom = ref(1)

const minZoom = 1
const maxZoom = 3
const zoomStep = 0.3
const scrollAmount = 300

const zoomStyle = computed(() => ({
  transform: `scale(${currentZoom.value})`,
}))

const zoomClass = computed(() => (currentZoom.value !== 1 ? 'zoomed' : ''))

function selectHotspot(artikel) {
  activeArtikel.value = artikel
}

function closeInfo() {
  activeArtikel.value = null
}

function scrollLeft() {
  stopPlay()
  scrollerRef.value?.scrollBy({ left: -scrollAmount, behavior: 'smooth' })
}

function scrollRight() {
  stopPlay()
  scrollerRef.value?.scrollBy({ left: scrollAmount, behavior: 'smooth' })
}

function zoomIn() {
  currentZoom.value = Math.min(maxZoom, currentZoom.value + zoomStep)
}

function zoomOut() {
  currentZoom.value = Math.max(minZoom, currentZoom.value - zoomStep)
}

function onDoubleClick(event) {
  const el = scrollerRef.value
  if (!el) return
  const rect = el.getBoundingClientRect()
  const x = ((event.clientX - rect.left) / rect.width) * 100
  const y = ((event.clientY - rect.top) / rect.height) * 100
  el.style.setProperty('--zoom-x', `${x}%`)
  el.style.setProperty('--zoom-y', `${y}%`)
  currentZoom.value = currentZoom.value === 1 ? 2 : 1
}

function toggleFullscreen() {
  const container = containerRef.value
  if (!container) return
  if (!document.fullscreenElement) {
    container.requestFullscreen?.()
  } else {
    document.exitFullscreen?.()
  }
}

function toggleWaypoints() {
  waypointsVisible.value = !waypointsVisible.value
}

let rafId = null
let lastTimestamp = null
let scrollAccumulator = 0
const playSpeed = 300

function step(timestamp) {
  const el = scrollerRef.value
  if (!isPlaying.value || !el) return

  if (!lastTimestamp) lastTimestamp = timestamp
  let deltaSec = (timestamp - lastTimestamp) / 1000
  deltaSec = Math.min(deltaSec, 0.1)
  lastTimestamp = timestamp

  const maxScroll = el.scrollWidth - el.clientWidth

  scrollAccumulator += playSpeed * deltaSec
  const wholePixels = Math.floor(scrollAccumulator)
  scrollAccumulator -= wholePixels

  if (wholePixels > 0) {
    el.scrollLeft += wholePixels
  }

  if (el.scrollLeft >= maxScroll) {
    el.scrollLeft = maxScroll
    stopPlay()
    return
  }

  rafId = requestAnimationFrame(step)
}

function startPlay() {
  const el = scrollerRef.value
  if (!el) return
  isPlaying.value = true
  lastTimestamp = null
  scrollAccumulator = 0
  el.style.scrollBehavior = 'auto'
  rafId = requestAnimationFrame(step)
}

function stopPlay() {
  isPlaying.value = false
  if (rafId) {
    cancelAnimationFrame(rafId)
    rafId = null
  }
  const el = scrollerRef.value
  if (el) el.style.scrollBehavior = ''
}

function togglePlay() {
  if (isPlaying.value) {
    stopPlay()
  } else {
    startPlay()
  }
}

function onUserInteraction() {
  if (isPlaying.value) stopPlay()
}

function onDocumentClick(event) {
  if (!activeArtikel.value) return
  const path = event.composedPath?.() ?? []
  const insidePanel = path.some(
    (el) =>
      el?.classList?.contains?.('panorama-info-panel') ||
      el?.classList?.contains?.('point-wrapper') ||
      el?.classList?.contains?.('hotspot-polygon') ||
      el?.classList?.contains?.('hotspot-polygon__shape'),
  )
  if (!insidePanel) closeInfo()
}

onMounted(() => {
  const el = scrollerRef.value
  if (el) {
    el.addEventListener('mousedown', onUserInteraction)
    el.addEventListener('touchstart', onUserInteraction, { passive: true })
  }
  document.addEventListener('click', onDocumentClick)
})

onUnmounted(() => {
  stopPlay()
  const el = scrollerRef.value
  if (el) {
    el.removeEventListener('mousedown', onUserInteraction)
    el.removeEventListener('touchstart', onUserInteraction)
  }
  document.removeEventListener('click', onDocumentClick)
})
</script>

<template>
  <div ref="containerRef" class="panorama" :class="{ 'waypoints-off': !waypointsVisible }">
    <div class="panorama-content">
      <div
        ref="scrollerRef"
        class="panorama-fotos"
        :class="zoomClass"
        :style="zoomStyle"
        @dblclick="onDoubleClick"
      >
        <div
          v-for="artikel in artikelen"
          :key="artikel.id"
          class="panorama-img-wrapper"
          :style="{
            zIndex: artikel.z_index,
            marginLeft: `${artikel.margin_left ?? 0}px`,
            marginTop: `${Math.max(-50, Math.min(175, artikel.margin_top ?? 100))}px`,
          }"
        >
          <img
            :src="artikel.afbeelding?.startsWith('data:') ? artikel.afbeelding : `/img/${artikel.afbeelding}`"
            :alt="artikel.alt"
          />
          <PolygonHotspot
            v-if="(Array.isArray(artikel.polygons) && artikel.polygons.length > 0) || Array.isArray(artikel.polygon)"
            :artikel="artikel"
            @select="selectHotspot"
          />
          <Hotspot
            v-if="artikel.x !== null && artikel.y !== null"
            :artikel="artikel"
            @select="selectHotspot"
          />
        </div>
      </div>

      <PanoramaControls
        :waypoints-visible="waypointsVisible"
        :is-playing="isPlaying"
        @zoom-in="zoomIn"
        @zoom-out="zoomOut"
        @fullscreen="toggleFullscreen"
        @open-intro="intro.open"
        @toggle-waypoints="toggleWaypoints"
        @toggle-play="togglePlay"
      />

      <button class="panorama-arrow panorama-arrow-left" type="button" aria-label="Scroll naar links" @click="scrollLeft">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6" />
        </svg>
      </button>
      <button class="panorama-arrow panorama-arrow-right" type="button" aria-label="Scroll naar rechts" @click="scrollRight">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </button>

      <Minimap :scroll-container="scrollerRef" :count="artikelen.length" />

      <InfoPanel :artikel="activeArtikel" @close="closeInfo" />
    </div>

    <IntroOverlay />
  </div>
</template>
