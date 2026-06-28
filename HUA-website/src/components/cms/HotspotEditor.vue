<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  imageSrc: { type: String, required: true },
  modelValueX: { type: Number, default: null },
  modelValueY: { type: Number, default: null },
})

const emit = defineEmits(['update:modelValueX', 'update:modelValueY'])

const wrapperRef = ref(null)
const imgRef = ref(null)
const naturalSize = ref({ width: 0, height: 0 })
const displaySize = ref({ width: 0, height: 0 })
const dragging = ref(false)

const hasHotspot = ref(props.modelValueX !== null && props.modelValueY !== null)
const internalX = ref(props.modelValueX ?? 0)
const internalY = ref(props.modelValueY ?? 0)

watch(() => props.modelValueX, (v) => { internalX.value = v ?? 0; hasHotspot.value = v !== null })
watch(() => props.modelValueY, (v) => { internalY.value = v ?? 0 })

function onImgLoad() {
  if (!imgRef.value) return
  naturalSize.value = {
    width: imgRef.value.naturalWidth,
    height: imgRef.value.naturalHeight,
  }
  const rect = imgRef.value.getBoundingClientRect()
  displaySize.value = { width: rect.width, height: rect.height }
}

function naturalToDisplay(value, axis) {
  const ratio = axis === 'x'
    ? displaySize.value.width / naturalSize.value.width
    : displaySize.value.height / naturalSize.value.height
  return value * ratio
}

function displayToNatural(value, axis) {
  const ratio = axis === 'x'
    ? naturalSize.value.width / displaySize.value.width
    : naturalSize.value.height / displaySize.value.height
  return Math.round(value * ratio)
}

function startDrag(event) {
  if (!hasHotspot.value) return
  dragging.value = true
  event.preventDefault()
}

function setFromEvent(clientX, clientY) {
  if (!imgRef.value) return
  const rect = imgRef.value.getBoundingClientRect()
  const dispX = Math.max(0, Math.min(rect.width, clientX - rect.left))
  const dispY = Math.max(0, Math.min(rect.height, clientY - rect.top))
  const natX = displayToNatural(dispX, 'x')
  const natY = displayToNatural(dispY, 'y')
  internalX.value = natX
  internalY.value = natY
  emit('update:modelValueX', natX)
  emit('update:modelValueY', natY)
}

function onMouseMove(event) {
  if (!dragging.value) return
  setFromEvent(event.clientX, event.clientY)
}

function onMouseUp() { dragging.value = false }

function onImageClick(event) {
  if (hasHotspot.value) return
  hasHotspot.value = true
  setFromEvent(event.clientX, event.clientY)
}

function verwijderHotspot() {
  hasHotspot.value = false
  internalX.value = 0
  internalY.value = 0
  emit('update:modelValueX', null)
  emit('update:modelValueY', null)
}

onMounted(() => {
  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
  window.addEventListener('resize', onImgLoad)
})

onUnmounted(() => {
  document.removeEventListener('mousemove', onMouseMove)
  document.removeEventListener('mouseup', onMouseUp)
  window.removeEventListener('resize', onImgLoad)
})
</script>

<template>
  <div>
    <div ref="wrapperRef" class="cms-hotspot-wrap" :style="{ maxWidth: '600px' }">
      <img
        ref="imgRef"
        :src="imageSrc"
        alt="hotspot positioneren"
        @load="onImgLoad"
        @click="onImageClick"
      />
      <div
        v-if="hasHotspot && naturalSize.width > 0"
        class="cms-hotspot"
        :style="{
          left: `${naturalToDisplay(internalX, 'x')}px`,
          top: `${naturalToDisplay(internalY, 'y')}px`,
        }"
        @mousedown="startDrag"
      >
        <span class="cms-hotspot__label">{{ internalX }}, {{ internalY }}</span>
      </div>
    </div>
    <div class="cms-btn-row" style="margin-top: 0.75rem;">
      <button v-if="hasHotspot" type="button" class="cms-btn cms-btn--ghost" @click="verwijderHotspot">Hotspot verwijderen</button>
      <small v-else style="color: #666;">Klik op de afbeelding om een hotspot te plaatsen.</small>
    </div>
  </div>
</template>
