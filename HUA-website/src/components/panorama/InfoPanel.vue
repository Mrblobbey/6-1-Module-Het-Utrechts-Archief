<script setup>
defineProps({
  artikel: { type: Object, default: null },
})

defineEmits(['close'])
</script>

<template>
  <div class="panorama-info-panel" :class="{ 'info-open': artikel !== null }">
    <button class="info-close" type="button" aria-label="Sluit informatie" @click="$emit('close')"></button>
    <div v-if="artikel" class="info-content">
      <div v-if="artikel.title" class="info-section info-section--titel">
        <h3>Titel</h3>
        <p class="info-title">{{ artikel.title }}</p>
      </div>

      <div class="info-section">
        <h3>Catalogus nummer</h3>
        <p>{{ artikel.catalogusnummer || 'Onbekend' }}</p>
      </div>

      <div class="info-section">
        <h3>Beschrijving</h3>
        <p>{{ artikel.beschrijving || 'Geen beschrijving beschikbaar.' }}</p>
      </div>

      <div v-if="artikel.link_bron" class="info-section">
        <h3>Meer informatie</h3>
        <p>
          <a :href="artikel.link_bron" target="_blank" rel="noopener noreferrer">
            Bekijken →
          </a>
        </p>
      </div>
    </div>
  </div>
</template>
