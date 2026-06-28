<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  imageSrc: { type: String, required: true },
  modelValue: { type: Array, default: null },
})

const emit = defineEmits(['update:modelValue', 'polygon-closed', 'polygon-bewerken'])

const viewportRef = ref(null)
const imgRef = ref(null)
const natural = ref({ width: 0, height: 0 })
const baseWidth = ref(600)

const polygons = ref([])
const activeIdx = ref(0)
const draggingPunt = ref({ polygonIdx: -1, puntIdx: -1 })
const zoom = ref(1)
const minZoom = 0.5
const maxZoom = 12
const cursor = ref(null)
const historyStack = ref([])

const SNAP_RADIUS_PX = 18

const mode = ref('klikken')
const rechthoekStart = ref(null)

const activePolygon = computed(() => polygons.value[activeIdx.value] ?? null)
const displayWidth = computed(() => baseWidth.value * zoom.value)
const displayHeight = computed(() =>
  natural.value.width > 0 ? (natural.value.height / natural.value.width) * displayWidth.value : 0,
)

const huidigeStaat = computed(() => {
  if (!activePolygon.value) return 'leeg'
  const n = activePolygon.value.points.length
  if (n === 0) return 'start'
  if (n < 3) return 'tekenen'
  return 'afsluitbaar'
})

const helperTekst = computed(() => {
  if (mode.value === 'rechthoek') {
    return rechthoekStart.value
      ? 'Klik op de tegenoverliggende hoek om de rechthoek te maken.'
      : 'Klik op de eerste hoek van de rechthoek.'
  }
  switch (huidigeStaat.value) {
    case 'leeg':
      return 'Klik op de afbeelding om je eerste hoekpunt te plaatsen.'
    case 'start':
      return 'Klik voor de volgende hoek.'
    case 'tekenen':
      return `Klik voor de volgende hoek (${activePolygon.value.points.length}/3 minimum).`
    case 'afsluitbaar':
      return `Klik op het rode startpunt om af te sluiten, of klik door voor meer hoeken.`
    default:
      return ''
  }
})

watch(
  () => props.modelValue,
  (val) => {
    if (Array.isArray(val) && val.length > 0) {
      polygons.value = val.map((p, i) => ({
        name: p?.name || `Gebied ${i + 1}`,
        beschrijving: p?.beschrijving || '',
        link_bron: p?.link_bron || '',
        points: Array.isArray(p?.points) ? p.points.map((pt) => ({ x: pt.x, y: pt.y })) : [],
      }))
    } else {
      polygons.value = []
    }
    if (activeIdx.value >= polygons.value.length) activeIdx.value = Math.max(0, polygons.value.length - 1)
  },
  { immediate: true },
)

function snapshot() {
  historyStack.value.push(JSON.stringify(polygons.value))
  if (historyStack.value.length > 50) historyStack.value.shift()
}

function ongedaanMaken() {
  if (historyStack.value.length === 0) return
  const prev = historyStack.value.pop()
  polygons.value = JSON.parse(prev)
  commit(false)
}

function onImageLoad() {
  if (!imgRef.value) return
  natural.value = { width: imgRef.value.naturalWidth, height: imgRef.value.naturalHeight }
  const containerW = viewportRef.value?.clientWidth ?? 600
  baseWidth.value = Math.min(natural.value.width, containerW - 16)
}

function naarNatural(clientX, clientY) {
  if (!imgRef.value) return { x: 0, y: 0 }
  const rect = imgRef.value.getBoundingClientRect()
  const dx = Math.max(0, Math.min(rect.width, clientX - rect.left))
  const dy = Math.max(0, Math.min(rect.height, clientY - rect.top))
  return {
    x: Math.round((dx / rect.width) * natural.value.width),
    y: Math.round((dy / rect.height) * natural.value.height),
  }
}

function naarDisplay(p) {
  if (!natural.value.width || !natural.value.height) return { x: 0, y: 0 }
  return {
    x: (p.x / natural.value.width) * displayWidth.value,
    y: (p.y / natural.value.height) * displayHeight.value,
  }
}

function commit(maakSnapshot = true) {
  if (maakSnapshot) snapshot()
  const cleaned = polygons.value
    .map((p) => ({ ...p, points: p.points.filter((pt) => pt) }))
    .filter((p) => p.points.length > 0)
  emit('update:modelValue', cleaned.length > 0 ? cleaned : null)
}

const polygonStringen = computed(() =>
  polygons.value.map((p) => p.points.map(naarDisplay).map((d) => `${d.x},${d.y}`).join(' ')),
)

const previewLineString = computed(() => {
  if (mode.value !== 'klikken') return null
  if (!cursor.value || !activePolygon.value || activePolygon.value.points.length === 0) return null
  const last = activePolygon.value.points[activePolygon.value.points.length - 1]
  const lastD = naarDisplay(last)
  return `${lastD.x},${lastD.y} ${cursor.value.x},${cursor.value.y}`
})

const previewRect = computed(() => {
  if (mode.value !== 'rechthoek' || !rechthoekStart.value || !cursor.value) return null
  const start = naarDisplay(rechthoekStart.value)
  return {
    x: Math.min(start.x, cursor.value.x),
    y: Math.min(start.y, cursor.value.y),
    width: Math.abs(start.x - cursor.value.x),
    height: Math.abs(start.y - cursor.value.y),
  }
})

function isGesloten(p) {
  return p && Array.isArray(p.points) && p.points.length >= 3 && p.points._gesloten
}

function isVoltooid(p) {
  return p && Array.isArray(p.points) && p.points.length >= 3
}

function nieuwePolygon() {
  snapshot()
  polygons.value.push({
    name: `Gebied ${polygons.value.length + 1}`,
    beschrijving: '',
    link_bron: '',
    points: [],
  })
  activeIdx.value = polygons.value.length - 1
}

function verwijderPolygon(idx) {
  if (!confirm('Deze polygon verwijderen?')) return
  snapshot()
  polygons.value.splice(idx, 1)
  if (activeIdx.value >= polygons.value.length) activeIdx.value = Math.max(0, polygons.value.length - 1)
  commit(false)
}

function selecteerPolygon(idx) {
  activeIdx.value = idx
}

function verwijderPunt(polyIdx, puntIdx) {
  snapshot()
  polygons.value[polyIdx].points.splice(puntIdx, 1)
  commit(false)
}

function dichtbijEerstePunt(clientX, clientY) {
  if (!activePolygon.value || activePolygon.value.points.length < 3) return false
  const eerste = activePolygon.value.points[0]
  const eersteD = naarDisplay(eerste)
  const rect = imgRef.value.getBoundingClientRect()
  const dx = clientX - rect.left - eersteD.x
  const dy = clientY - rect.top - eersteD.y
  return Math.sqrt(dx * dx + dy * dy) < SNAP_RADIUS_PX
}

function sluitActievePolygon() {
  if (!activePolygon.value || activePolygon.value.points.length < 3) return
  const idx = activeIdx.value
  emit('polygon-closed', idx)
  nieuwePolygon()
}

function onImageClick(event) {
  if (event.detail >= 2) return
  if (draggingPunt.value.polygonIdx !== -1) return

  if (mode.value === 'rechthoek') {
    handleRechthoekClick(event)
    return
  }

  if (polygons.value.length === 0) nieuwePolygon()
  if (dichtbijEerstePunt(event.clientX, event.clientY)) {
    sluitActievePolygon()
    return
  }
  const poly = polygons.value[activeIdx.value]
  if (!poly) return
  snapshot()
  poly.points.push(naarNatural(event.clientX, event.clientY))
  commit(false)
}

function handleRechthoekClick(event) {
  const punt = naarNatural(event.clientX, event.clientY)
  if (!rechthoekStart.value) {
    rechthoekStart.value = punt
    return
  }
  const start = rechthoekStart.value
  rechthoekStart.value = null
  const minX = Math.min(start.x, punt.x)
  const maxX = Math.max(start.x, punt.x)
  const minY = Math.min(start.y, punt.y)
  const maxY = Math.max(start.y, punt.y)
  snapshot()
  polygons.value.push({
    name: `Gebied ${polygons.value.length + 1}`,
    beschrijving: '',
    link_bron: '',
    points: [
      { x: minX, y: minY },
      { x: maxX, y: minY },
      { x: maxX, y: maxY },
      { x: minX, y: maxY },
    ],
  })
  activeIdx.value = polygons.value.length - 1
  commit(false)
  emit('polygon-closed', activeIdx.value)
  mode.value = 'klikken'
}

function zetMode(nieuw) {
  mode.value = nieuw
  rechthoekStart.value = null
  if (nieuw !== 'klikken' && activePolygon.value && activePolygon.value.points.length === 0) {
    polygons.value.pop()
    activeIdx.value = Math.max(0, polygons.value.length - 1)
  }
}

function onEerstePuntClick(polyIdx) {
  if (polyIdx !== activeIdx.value) {
    selecteerPolygon(polyIdx)
    return
  }
  sluitActievePolygon()
}

function startPuntDrag(polyIdx, puntIdx, event) {
  draggingPunt.value = { polygonIdx: polyIdx, puntIdx }
  activeIdx.value = polyIdx
  event.preventDefault()
  event.stopPropagation()
}

function onMouseMove(event) {
  const { polygonIdx, puntIdx } = draggingPunt.value
  if (polygonIdx !== -1) {
    const poly = polygons.value[polygonIdx]
    if (!poly || !poly.points[puntIdx]) return
    poly.points[puntIdx] = naarNatural(event.clientX, event.clientY)
    commit(false)
    return
  }
  if (imgRef.value) {
    const rect = imgRef.value.getBoundingClientRect()
    if (
      event.clientX >= rect.left &&
      event.clientX <= rect.right &&
      event.clientY >= rect.top &&
      event.clientY <= rect.bottom
    ) {
      cursor.value = {
        x: event.clientX - rect.left,
        y: event.clientY - rect.top,
      }
    } else {
      cursor.value = null
    }
  }
}

function onMouseUp() {
  if (draggingPunt.value.polygonIdx !== -1) {
    draggingPunt.value = { polygonIdx: -1, puntIdx: -1 }
    commit()
  }
}

function onWheel(event) {
  event.preventDefault()
  const delta = event.deltaY < 0 ? 0.15 : -0.15
  zoom.value = Math.max(minZoom, Math.min(maxZoom, zoom.value + delta))
}

function resetZoom() {
  zoom.value = 1
}

function onKeyDown(event) {
  if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') return
  if ((event.ctrlKey || event.metaKey) && event.key === 'z') {
    event.preventDefault()
    ongedaanMaken()
  } else if (event.key === 'Escape' && activePolygon.value?.points.length > 0) {
    if (activePolygon.value.points.length >= 3) {
      sluitActievePolygon()
    }
  }
}

onMounted(() => {
  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
  document.addEventListener('keydown', onKeyDown)
  window.addEventListener('resize', onImageLoad)
})

onUnmounted(() => {
  document.removeEventListener('mousemove', onMouseMove)
  document.removeEventListener('mouseup', onMouseUp)
  document.removeEventListener('keydown', onKeyDown)
  window.removeEventListener('resize', onImageLoad)
})

defineExpose({ selecteerPolygon })
</script>

<template>
  <div>
    <div class="poly-toolbar">
      <div class="poly-modes">
        <button
          type="button"
          class="poly-mode"
          :class="{ 'is-active': mode === 'klikken' }"
          @click="zetMode('klikken')"
          title="Klik per hoekpunt. Klik op rode startpunt om af te sluiten."
        >
          🖋 Lijnen tekenen
        </button>
        <button
          type="button"
          class="poly-mode"
          :class="{ 'is-active': mode === 'rechthoek' }"
          @click="zetMode('rechthoek')"
          title="Klik 2 hoeken — werkt perfect voor blokken"
        >
          ▭ Rechthoek
        </button>
      </div>

      <button
        type="button"
        class="cms-btn cms-btn--ghost poly-undo"
        :disabled="historyStack.length === 0"
        @click="ongedaanMaken"
        title="Ongedaan maken (Ctrl+Z)"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 7v6h6" />
          <path d="M3 13a9 9 0 1 0 3-7.7L3 8" />
        </svg>
        Ongedaan
      </button>
      <button
        v-if="mode === 'klikken' && activePolygon && activePolygon.points.length >= 3"
        type="button"
        class="cms-btn cms-btn--secondary"
        @click="sluitActievePolygon"
      >
        ✓ Polygon afsluiten
      </button>

      <div class="poly-zoom">
        Zoom: <strong>{{ Math.round(zoom * 100) }}%</strong>
        <button type="button" @click="resetZoom">Reset</button>
      </div>
    </div>


    <div v-if="polygons.length > 0" class="poly-list">
      <button
        v-for="(poly, idx) in polygons"
        :key="idx"
        type="button"
        class="poly-list__item"
        :class="{ 'is-active': idx === activeIdx }"
        @click="selecteerPolygon(idx)"
      >
        <strong>#{{ idx + 1 }}</strong>
        <span>{{ poly.name }}</span>
        <span class="poly-list__count">{{ poly.points.length }}h {{ isVoltooid(poly) ? '✓' : '…' }}</span>
        <span class="poly-list__remove" @click.stop="verwijderPolygon(idx)" title="Verwijder polygon">×</span>
      </button>
    </div>

    <div class="poly-hint" :class="`poly-hint--${huidigeStaat}`">
      <span class="poly-hint__step">{{ activeIdx + 1 }}</span>
      {{ helperTekst }}
    </div>

    <div ref="viewportRef" class="poly-viewport" @wheel="onWheel">
      <div
        class="poly-canvas"
        :style="{ width: `${displayWidth}px`, height: `${displayHeight}px` }"
      >
        <img
          ref="imgRef"
          :src="imageSrc"
          alt="polygon tekenen"
          draggable="false"
          :style="{ width: `${displayWidth}px`, height: `${displayHeight}px` }"
          @load="onImageLoad"
          @click="onImageClick"
        />
        <svg
          v-if="natural.width > 0 && (polygons.length > 0 || previewLineString)"
          class="poly-svg"
          :viewBox="`0 0 ${displayWidth} ${displayHeight}`"
          preserveAspectRatio="none"
          :style="{ width: `${displayWidth}px`, height: `${displayHeight}px` }"
        >
          <template v-for="(poly, polyIdx) in polygons" :key="polyIdx">
            <polyline
              v-if="!isVoltooid(poly) && poly.points.length >= 2"
              :points="polygonStringen[polyIdx]"
              fill="none"
              :stroke="polyIdx === activeIdx ? '#e44421' : 'rgba(228,68,33,0.5)'"
              :stroke-width="polyIdx === activeIdx ? 2 : 1.5"
              stroke-dasharray="6 4"
            />
            <polygon
              v-else-if="isVoltooid(poly)"
              :points="polygonStringen[polyIdx]"
              :fill="polyIdx === activeIdx ? 'rgba(228, 68, 33, 0.25)' : 'rgba(228, 68, 33, 0.12)'"
              :stroke="polyIdx === activeIdx ? '#e44421' : 'rgba(228,68,33,0.55)'"
              :stroke-width="polyIdx === activeIdx ? 2.5 : 1.5"
              style="cursor: pointer;"
              @click.stop="selecteerPolygon(polyIdx)"
              @dblclick.stop="$emit('polygon-bewerken', polyIdx)"
            >
              <title>{{ poly.name }} (dubbelklik om info aan te passen)</title>
            </polygon>
          </template>
          <polyline
            v-if="previewLineString"
            :points="previewLineString"
            fill="none"
            stroke="#e44421"
            stroke-width="1.5"
            stroke-dasharray="4 3"
            opacity="0.7"
            pointer-events="none"
          />
          <rect
            v-if="previewRect"
            :x="previewRect.x"
            :y="previewRect.y"
            :width="previewRect.width"
            :height="previewRect.height"
            fill="rgba(228,68,33,0.18)"
            stroke="#e44421"
            stroke-width="2"
            stroke-dasharray="6 4"
            pointer-events="none"
          />
        </svg>
        <template v-for="(poly, polyIdx) in polygons" :key="`handles-${polyIdx}`">
          <button
            v-for="(p, puntIdx) in poly.points.map(naarDisplay)"
            :key="puntIdx"
            class="poly-handle"
            :class="{
              'poly-handle--active': polyIdx === activeIdx,
              'poly-handle--first': puntIdx === 0,
              'poly-handle--first-closable':
                puntIdx === 0 && polyIdx === activeIdx && poly.points.length >= 3 && !isGesloten(poly),
            }"
            :style="{ left: `${p.x}px`, top: `${p.y}px` }"
            :title="
              puntIdx === 0 && poly.points.length >= 3
                ? 'Klik om polygon af te sluiten'
                : `Polygon ${polyIdx + 1} punt ${puntIdx + 1} (sleep of dubbelklik om te verwijderen)`
            "
            @mousedown="startPuntDrag(polyIdx, puntIdx, $event)"
            @click.stop="
              puntIdx === 0 && poly.points.length >= 3
                ? onEerstePuntClick(polyIdx)
                : null
            "
            @dblclick.stop="verwijderPunt(polyIdx, puntIdx)"
          >
            {{ puntIdx + 1 }}
          </button>
        </template>
      </div>
    </div>

    <small class="poly-help">
      <strong>Klik</strong> = punt · <strong>klik op rode startpunt</strong> = sluiten ·
      <strong>Esc</strong> = nu sluiten · <strong>Ctrl+Z</strong> = ongedaan ·
      <strong>sleep</strong> punten = verplaatsen · <strong>dubbelklik op punt</strong> = verwijderen ·
      <strong>scrollwiel</strong> = zoom
    </small>
  </div>
</template>

<style scoped>
.poly-toolbar {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 0.6rem;
  flex-wrap: wrap;
}

.poly-undo {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
}

.poly-undo svg {
  width: 16px;
  height: 16px;
  display: block;
}

.poly-modes {
  display: inline-flex;
  background: #f0f0f0;
  padding: 3px;
  border-radius: 6px;
  gap: 2px;
}

.poly-mode {
  background: transparent;
  border: none;
  padding: 0.45rem 0.85rem;
  border-radius: 4px;
  cursor: pointer;
  font-family: inherit;
  font-size: 0.88rem;
  font-weight: 600;
  color: #555;
}

.poly-mode:hover {
  background: rgba(255, 255, 255, 0.6);
}

.poly-mode.is-active {
  background: #fff;
  color: var(--red);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
}


.poly-zoom {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.85rem;
  color: #555;
  margin-left: auto;
}

.poly-zoom button {
  padding: 0.2rem 0.6rem;
  border: 1px solid #ccc;
  background: #fff;
  border-radius: 3px;
  cursor: pointer;
  font-family: inherit;
}

.poly-list {
  display: flex;
  gap: 0.4rem;
  flex-wrap: wrap;
  margin-bottom: 0.6rem;
}

.poly-list__item {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0.6rem 0.35rem 0.7rem;
  background: #fff;
  border: 2px solid #ddd;
  border-radius: 4px;
  font-family: inherit;
  font-size: 0.85rem;
  cursor: pointer;
}

.poly-list__item.is-active {
  border-color: var(--red);
  background: rgba(228, 68, 33, 0.08);
}

.poly-list__item strong {
  color: var(--red);
}

.poly-list__item span {
  color: #555;
}

.poly-list__count {
  font-size: 0.78rem;
  opacity: 0.7;
}

.poly-list__remove {
  background: #f5f5f5;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  font-weight: 700;
  color: #b02314;
}

.poly-list__remove:hover {
  background: var(--red);
  color: #fff;
}

.poly-hint {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.55rem 0.85rem;
  background: #fff7e0;
  border-left: 3px solid #f3c33b;
  border-radius: 4px;
  font-size: 0.9rem;
  color: #5a4500;
  margin-bottom: 0.6rem;
}

.poly-hint--afsluitbaar {
  background: #d1f0d9;
  border-left-color: #166a5a;
  color: #0d473d;
}

.poly-hint__step {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  background: rgba(0, 0, 0, 0.12);
  border-radius: 50%;
  font-weight: 700;
  font-size: 0.78rem;
}

.poly-viewport {
  position: relative;
  width: 100%;
  max-width: 700px;
  height: 500px;
  border: 1px solid #ccc;
  border-radius: 4px;
  overflow: auto;
  background: #f5f5f5;
  cursor: crosshair;
}

.poly-canvas {
  position: relative;
  display: block;
}

.poly-canvas img {
  display: block;
  user-select: none;
  -webkit-user-drag: none;
}

.poly-svg {
  position: absolute;
  top: 0;
  left: 0;
  pointer-events: none;
}

.poly-svg polygon,
.poly-svg polyline {
  pointer-events: auto;
}

.poly-handle {
  position: absolute;
  width: 24px;
  height: 24px;
  background: #fff;
  color: #999;
  border: 2px solid #999;
  border-radius: 50%;
  font-size: 0.72rem;
  font-weight: 700;
  font-family: inherit;
  cursor: grab;
  transform: translate(-50%, -50%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  z-index: 2;
}

.poly-handle--active {
  color: var(--red);
  border-color: var(--red);
}

.poly-handle--first {
  background: var(--red);
  color: #fff;
  border-color: var(--red);
}

.poly-handle--first-closable {
  width: 36px;
  height: 36px;
  font-size: 0.95rem;
  box-shadow: 0 0 0 4px rgba(228, 68, 33, 0.25), 0 2px 8px rgba(228, 68, 33, 0.4);
  cursor: pointer;
  animation: closeHandlePulse 1.4s ease-in-out infinite;
}

@keyframes closeHandlePulse {
  0%, 100% {
    box-shadow: 0 0 0 4px rgba(228, 68, 33, 0.25), 0 2px 8px rgba(228, 68, 33, 0.4);
  }
  50% {
    box-shadow: 0 0 0 10px rgba(228, 68, 33, 0.15), 0 2px 14px rgba(228, 68, 33, 0.55);
  }
}

.poly-handle:active {
  cursor: grabbing;
}

.poly-help {
  display: block;
  margin-top: 0.5rem;
  color: #666;
  font-size: 0.8rem;
  line-height: 1.5;
}
</style>
