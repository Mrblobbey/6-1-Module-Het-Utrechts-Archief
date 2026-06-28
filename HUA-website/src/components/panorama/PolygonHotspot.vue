<script setup>
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
  artikel: { type: Object, required: true },
})

const emit = defineEmits(['select'])

const imgRef = ref(null)
const natural = ref({ width: 0, height: 0 })

const polygons = computed(() => {
  if (Array.isArray(props.artikel.polygons)) {
    return props.artikel.polygons
      .map((p, i) =>
        Array.isArray(p)
          ? { name: `Gebied ${i + 1}`, beschrijving: '', link_bron: '', points: p }
          : p,
      )
      .filter((p) => Array.isArray(p.points) && p.points.length >= 3)
  }
  if (Array.isArray(props.artikel.polygon) && props.artikel.polygon.length >= 3) {
    return [{ name: 'Gebied', beschrijving: '', link_bron: '', points: props.artikel.polygon }]
  }
  return []
})

const polygonPunten = computed(() =>
  polygons.value.map((p) => p.points.map((pt) => `${pt.x},${pt.y}`).join(' ')),
)

function meet() {
  if (!imgRef.value) return
  natural.value = {
    width: imgRef.value.naturalWidth || imgRef.value.width,
    height: imgRef.value.naturalHeight || imgRef.value.height,
  }
}

function selectPolygon(polygon) {
  emit('select', {
    catalogusnummer: polygon.catalogusnummer || props.artikel.catalogusnummer,
    title: polygon.title || polygon.name || props.artikel.alt || '',
    beschrijving: polygon.beschrijving || props.artikel.beschrijving,
    link_bron: polygon.link_bron || props.artikel.link_bron,
  })
}

onMounted(meet)
</script>

<template>
  <svg
    v-if="polygons.length > 0"
    class="hotspot-polygon"
    :viewBox="`0 0 ${natural.width || 1188} ${natural.height || 600}`"
    preserveAspectRatio="none"
  >
    <polygon
      v-for="(poly, idx) in polygons"
      :key="idx"
      class="hotspot-polygon__shape"
      :points="polygonPunten[idx]"
      @click.stop="selectPolygon(poly)"
    >
      <title>{{ poly.name }}</title>
    </polygon>
  </svg>
  <img
    ref="imgRef"
    style="display: none;"
    :src="artikel.afbeelding?.startsWith('data:') ? artikel.afbeelding : `/img/${artikel.afbeelding}`"
    alt=""
    @load="meet"
  />
</template>

<style scoped>
.hotspot-polygon {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: var(--panorama-foto-height, 665px);
  pointer-events: none;
  z-index: 900;
  overflow: visible;
}

.hotspot-polygon__shape {
  fill: rgba(228, 68, 33, 0.08);
  stroke: rgba(228, 68, 33, 0.55);
  stroke-width: 2;
  vector-effect: non-scaling-stroke;
  pointer-events: auto;
  cursor: pointer;
  transition: fill 0.2s ease, stroke 0.2s ease, filter 0.2s ease;
}

.hotspot-polygon__shape:hover {
  fill: rgba(228, 68, 33, 0.32);
  stroke: #e44421;
  stroke-width: 3;
  filter: drop-shadow(0 0 12px rgba(228, 68, 33, 0.85));
  animation: hotspotPulse 1.4s ease-in-out infinite;
}

@keyframes hotspotPulse {
  0%, 100% {
    filter: drop-shadow(0 0 6px rgba(228, 68, 33, 0.6));
  }
  50% {
    filter: drop-shadow(0 0 18px rgba(228, 68, 33, 0.95));
  }
}
</style>
