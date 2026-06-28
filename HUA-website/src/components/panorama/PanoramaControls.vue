<script setup>
defineProps({
  waypointsVisible: { type: Boolean, default: true },
  isPlaying: { type: Boolean, default: false },
})

defineEmits(['zoom-in', 'zoom-out', 'fullscreen', 'open-intro', 'toggle-waypoints', 'toggle-play'])
</script>

<template>
  <div class="panorama-controls">
    <button type="button" class="pano-btn pano-zoom-in" aria-label="Zoom in" @click.stop="$emit('zoom-in')">+</button>
    <button type="button" class="pano-btn pano-zoom-out" aria-label="Zoom uit" @click.stop="$emit('zoom-out')">−</button>
    <button type="button" class="pano-btn pano-fullscreen" aria-label="Volledig scherm" @click.stop="$emit('fullscreen')">⤢</button>
    <button type="button" class="panorama-help-btn" aria-label="Help" @click.stop="$emit('open-intro')">?</button>
    <button
      type="button"
      class="pano-btn pano-waypoints"
      :class="{ 'is-off': !waypointsVisible }"
      aria-label="Waypoints aan/uit"
      @click.stop="$emit('toggle-waypoints')"
    ></button>
    <button
      type="button"
      class="pano-btn pano-play"
      :class="{ 'is-playing': isPlaying }"
      aria-label="Automatisch afspelen"
      @click.stop="$emit('toggle-play')"
    ></button>
  </div>
</template>
