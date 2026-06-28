<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useArtikelenStore } from '@/stores/artikelen'

const store = useArtikelenStore()

const editorRef = ref(null)
const working = ref([])
const activeId = ref(null)
const saving = ref(false)
const msg = ref(null)

const dragState = ref(null)
const zoom = ref(1)
const minZoom = 0.4
const maxZoom = 2.5
const zoomPercent = computed(() => Math.round(zoom.value * 100))

const MARGIN_TOP_MIN = -50
const MARGIN_TOP_MAX = 175

function clampMarginTop(v) {
  return Math.max(MARGIN_TOP_MIN, Math.min(MARGIN_TOP_MAX, Number(v) || 0))
}

function onWheel(event) {
  event.preventDefault()
  const delta = event.deltaY < 0 ? 0.1 : -0.1
  zoom.value = Math.max(minZoom, Math.min(maxZoom, zoom.value + delta))
}

function resetZoom() {
  zoom.value = 1
}

const active = computed(() =>
  working.value.find((t) => String(t.id) === String(activeId.value)),
)

function bindActive(field) {
  return computed({
    get: () => active.value?.[field] ?? 0,
    set: (v) => {
      if (!active.value) return
      const num = Number(v) || 0
      active.value[field] = field === 'margin_top' ? clampMarginTop(num) : num
    },
  })
}

const activeMarginLeft = bindActive('margin_left')
const activeMarginTop = bindActive('margin_top')
const activeZIndex = computed(() => active.value?.z_index ?? null)

async function init() {
  await store.laden()
  working.value = store.sorted.map((a) => ({
    id: a.id,
    afbeelding: a.afbeelding,
    margin_left: Number(a.margin_left ?? 0),
    margin_top: clampMarginTop(a.margin_top ?? 100),
    z_index: Number(a.z_index ?? a.id ?? 0),
  }))
  if (working.value.length && !activeId.value) activeId.value = working.value[0].id
}

onMounted(init)

function imageUrl(tile) {
  if (!tile.afbeelding) return ''
  return tile.afbeelding.startsWith('data:') ? tile.afbeelding : `/img/${tile.afbeelding}`
}

function selecteer(id) {
  activeId.value = id
  editorRef.value?.focus()
}

function onKeyDown(event) {
  if (!active.value) return
  const key = event.key
  if (!['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(key)) return
  event.preventDefault()
  const step = event.shiftKey ? 10 : 1
  if (key === 'ArrowLeft') active.value.margin_left -= step
  if (key === 'ArrowRight') active.value.margin_left += step
  if (key === 'ArrowUp') active.value.margin_top = clampMarginTop(active.value.margin_top - step)
  if (key === 'ArrowDown') active.value.margin_top = clampMarginTop(active.value.margin_top + step)
}

function startDrag(event, tile) {
  selecteer(tile.id)
  dragState.value = {
    id: tile.id,
    startX: event.clientX,
    startY: event.clientY,
    startMarginLeft: tile.margin_left,
    startMarginTop: tile.margin_top,
    shiftHeld: event.shiftKey,
  }
  event.preventDefault()
}

function onMouseMove(event) {
  if (!dragState.value) return
  const tile = working.value.find((t) => t.id === dragState.value.id)
  if (!tile) return
  const dx = (event.clientX - dragState.value.startX) / zoom.value
  const dy = (event.clientY - dragState.value.startY) / zoom.value
  tile.margin_left = Math.round(dragState.value.startMarginLeft + dx)
  tile.margin_top = clampMarginTop(Math.round(dragState.value.startMarginTop + dy))
}

function onMouseUp() {
  dragState.value = null
}

function bringForward() {
  if (!active.value) return
  const maxZ = Math.max(0, ...working.value.map((t) => Number(t.z_index) || 0))
  active.value.z_index = maxZ + 1
}

function sendBackward() {
  if (!active.value) return
  const minZ = Math.min(0, ...working.value.map((t) => Number(t.z_index) || 0))
  active.value.z_index = minZ - 1
}

function reset() {
  if (!confirm('Alle posities terugzetten naar de standaard?')) return
  working.value.forEach((t, i) => {
    t.margin_left = 0
    t.margin_top = 100
    t.z_index = i + 1
  })
}

async function opslaan() {
  saving.value = true
  const result = await store.bulkLayoutOpslaan(
    working.value.map((t) => ({
      id: t.id,
      margin_left: Number(t.margin_left) || 0,
      margin_top: clampMarginTop(t.margin_top),
      z_index: Number(t.z_index) || 0,
    })),
  )
  saving.value = false
  if (result.success) {
    msg.value = { type: 'success', text: 'Posities opgeslagen.' }
  } else {
    msg.value = { type: 'error', text: result.error }
  }
  setTimeout(() => (msg.value = null), 3000)
}

onMounted(() => {
  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
})

onUnmounted(() => {
  document.removeEventListener('mousemove', onMouseMove)
  document.removeEventListener('mouseup', onMouseUp)
})
</script>

<template>
  <div v-if="msg" :class="['cms-alert', msg.type === 'success' ? 'cms-alert--success' : 'cms-alert--error']">
    {{ msg.text }}
  </div>

  <div class="cms-panel">
    <h3 class="cms-panel__title">Panorama-layout — foto's mooi laten aansluiten</h3>
    <p style="color: #666; margin-top: 0; display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
      <span>Sleep een foto vrij in alle richtingen, of gebruik <kbd>← ↑ → ↓</kbd> (Shift = 10px). Scroll met je muiswiel om in/uit te zoomen.</span>
      <span class="layout-editor__zoom">
        Zoom: <strong>{{ zoomPercent }}%</strong>
        <button type="button" @click="resetZoom">Reset</button>
      </span>
    </p>

    <div ref="editorRef" class="layout-editor" tabindex="0" @wheel="onWheel" @keydown="onKeyDown">
      <div class="layout-editor__scroll">
        <div class="layout-editor__strip" :style="{ zoom }">
          <div
            v-for="tile in working"
            :key="tile.id"
            class="layout-tile"
            :class="{ 'is-active': tile.id === activeId, 'is-dragging': dragState?.id === tile.id }"
            :style="{
              marginLeft: `${tile.margin_left}px`,
              marginTop: `${tile.margin_top - 100}px`,
              zIndex: tile.z_index,
            }"
            @mousedown="startDrag($event, tile)"
          >
            <img v-if="tile.afbeelding" :src="imageUrl(tile)" :alt="`Tile ${tile.id}`" draggable="false" />
            <div class="layout-tile__handle">#{{ tile.id }}</div>
          </div>
        </div>
      </div>
      <div class="layout-editor__baseline"></div>
      <div class="layout-editor__midline"></div>
    </div>

    <div class="layout-controls">
      <div class="layout-controls__group">
        <label>Geselecteerd:</label>
        <strong>#{{ active?.id ?? '—' }}</strong>
      </div>

      <div class="layout-controls__group">
        <label for="ml">margin_left</label>
        <input id="ml" v-model.number="activeMarginLeft" type="number" :disabled="!active" />
      </div>

      <div class="layout-controls__group">
        <label for="mt">margin_top</label>
        <input id="mt" v-model.number="activeMarginTop" type="number" :disabled="!active" />
      </div>

      <div class="layout-controls__group">
        <label>z-index</label>
        <button class="cms-btn cms-btn--ghost" :disabled="!active" @click="sendBackward">↓</button>
        <strong style="min-width: 30px; text-align: center;">{{ activeZIndex ?? '—' }}</strong>
        <button class="cms-btn cms-btn--ghost" :disabled="!active" @click="bringForward">↑</button>
      </div>
    </div>

    <div class="cms-btn-row" style="margin-top: 1rem;">
      <button class="cms-btn cms-btn--ghost" @click="reset">Reset alle posities</button>
      <button class="cms-btn cms-btn--primary" :disabled="saving" @click="opslaan">
        {{ saving ? 'Opslaan…' : 'Opslaan' }}
      </button>
    </div>
  </div>
</template>
