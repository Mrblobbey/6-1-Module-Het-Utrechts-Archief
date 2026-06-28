<script setup>
import { ref, watch, onUnmounted } from 'vue'

const props = defineProps({
  scrollContainer: { type: [Object, null], default: null },
  count: { type: Number, default: 33 },
})

const thumbnailsRef = ref(null)
const indicatorRef = ref(null)
const indicatorWidth = ref(0)
const indicatorLeft = ref(0)
const activeIdx = ref(0)
const dragging = ref(false)

function clamp(v, min, max) {
  return Math.max(min, Math.min(max, v))
}

function getWrappers() {
  return props.scrollContainer?.querySelectorAll('.panorama-img-wrapper') ?? []
}

function dichtstbijzijndeIdx(scrollLeft, viewerWidth) {
  const wrappers = getWrappers()
  if (wrappers.length === 0) return 0
  const centerPx = scrollLeft + viewerWidth / 2
  let bestIdx = 0
  let bestDelta = Infinity
  for (let i = 0; i < wrappers.length; i++) {
    const w = wrappers[i]
    const wCenter = w.offsetLeft + w.offsetWidth / 2
    const delta = Math.abs(wCenter - centerPx)
    if (delta < bestDelta) {
      bestDelta = delta
      bestIdx = i
    }
  }
  return bestIdx
}

let updateRafId = null
function updateIndicator() {
  if (updateRafId) return
  updateRafId = requestAnimationFrame(() => {
    updateRafId = null
    const viewer = props.scrollContainer
    const thumbs = thumbnailsRef.value
    if (!viewer || !thumbs) return

    const totalWidth = viewer.scrollWidth
    const viewerWidth = viewer.clientWidth
    if (totalWidth <= 0) return

    const minimapWidth = thumbs.clientWidth
    const ratioVisible = viewerWidth / totalWidth
    const visibleWidth = minimapWidth * ratioVisible

    activeIdx.value = dichtstbijzijndeIdx(viewer.scrollLeft, viewerWidth)

    const thumbBtns = thumbs.querySelectorAll('.minimap-thumb-btn')
    const activeBtn = thumbBtns[activeIdx.value]
    if (activeBtn) {
      const thumbCenter = activeBtn.offsetLeft + activeBtn.offsetWidth / 2
      indicatorLeft.value = clamp(thumbCenter - visibleWidth / 2, 0, minimapWidth - visibleWidth)
    } else {
      const maxScroll = Math.max(1, totalWidth - viewerWidth)
      const ratioScroll = viewer.scrollLeft / maxScroll
      const maxLeft = Math.max(0, minimapWidth - visibleWidth)
      indicatorLeft.value = ratioScroll * maxLeft
    }

    indicatorWidth.value = visibleWidth
  })
}

function gotoIndex(i) {
  const viewer = props.scrollContainer
  if (!viewer) return

  const wrappers = getWrappers()
  const target = wrappers[i]
  if (!target) return

  const totalWidth = viewer.scrollWidth
  const viewerWidth = viewer.clientWidth
  const maxScroll = Math.max(0, totalWidth - viewerWidth)
  const targetCenter = target.offsetLeft + target.offsetWidth / 2
  const targetScroll = clamp(targetCenter - viewerWidth / 2, 0, maxScroll)

  viewer.scrollTo({ left: targetScroll, behavior: 'smooth' })
  activeIdx.value = i
}

function startDrag(event) {
  dragging.value = true
  event.preventDefault()
}

function onMouseMove(event) {
  if (!dragging.value) return
  const viewer = props.scrollContainer
  const thumbs = thumbnailsRef.value
  if (!viewer || !thumbs) return

  const rect = thumbs.getBoundingClientRect()
  const minimapWidth = rect.width
  const totalWidth = viewer.scrollWidth
  const viewerWidth = viewer.clientWidth
  const maxScroll = Math.max(0, totalWidth - viewerWidth)

  const x = clamp(event.clientX - rect.left, 0, minimapWidth)
  const percent = x / minimapWidth
  const targetScroll = clamp(percent * totalWidth - viewerWidth / 2, 0, maxScroll)

  viewer.scrollLeft = targetScroll
  updateIndicator()
}

function onMouseUp() {
  dragging.value = false
}

function clickStrip(event) {
  if (event.target === indicatorRef.value || indicatorRef.value?.contains(event.target)) return
  const thumbs = thumbnailsRef.value
  if (!thumbs) return
  const rect = thumbs.getBoundingClientRect()
  const x = clamp(event.clientX - rect.left, 0, rect.width)
  const idx = clamp(Math.floor((x / rect.width) * props.count), 0, props.count - 1)
  gotoIndex(idx)
}

let cleanupViewer = null

watch(
  () => props.scrollContainer,
  (viewer) => {
    cleanupViewer?.()
    if (!viewer) return

    viewer.addEventListener('scroll', updateIndicator)
    window.addEventListener('resize', updateIndicator)
    document.addEventListener('mousemove', onMouseMove)
    document.addEventListener('mouseup', onMouseUp)

    setTimeout(updateIndicator, 200)

    cleanupViewer = () => {
      viewer.removeEventListener('scroll', updateIndicator)
      window.removeEventListener('resize', updateIndicator)
      document.removeEventListener('mousemove', onMouseMove)
      document.removeEventListener('mouseup', onMouseUp)
    }
  },
  { immediate: true },
)

onUnmounted(() => cleanupViewer?.())
</script>

<template>
  <div class="minimap-bottom">
    <div ref="thumbnailsRef" class="minimap-thumbnails" @click="clickStrip">
      <button
        v-for="i in count"
        :key="i"
        type="button"
        class="minimap-thumb-btn"
        :class="{ 'is-active': i - 1 === activeIdx }"
        :title="`Spring naar foto ${i}`"
        :data-index="i"
        @click.stop="gotoIndex(i - 1)"
      >
        <img :src="`/img-afgesneden/${i}.jpg`" :alt="`Deel ${i}`" class="minimap-thumb" />
        <span class="minimap-thumb-label">{{ i }}</span>
      </button>
      <div
        ref="indicatorRef"
        class="minimap-viewport-indicator"
        :class="{ dragging }"
        :style="{ left: `${indicatorLeft}px`, width: `${indicatorWidth}px` }"
        @mousedown="startDrag"
      ></div>
    </div>
  </div>
</template>
