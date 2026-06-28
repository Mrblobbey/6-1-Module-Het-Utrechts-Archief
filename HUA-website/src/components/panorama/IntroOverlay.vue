<script setup>
import { useIntroStore } from '@/stores/intro'

const intro = useIntroStore()

function handleOverlayClick(event) {
  if (event.target === event.currentTarget) {
    intro.sluit()
  }
}
</script>

<template>
  <div v-if="intro.visible" class="intro-overlay" @click="handleOverlayClick">
    <div class="intro-modal">
      <button class="intro-close" type="button" aria-label="Sluit uitleg" @click="intro.sluit">×</button>

      <h2>Panorama van Utrecht – Uitleg</h2>
      <ul>
        <li>Gebruik de pijltjes links en rechts om door het panorama te schuiven.</li>
        <li>Klik in de minimap rechtsonder om snel naar een andere plek te springen.</li>
        <li>Dubbelklik op het panorama om in of uit te zoomen op de plek waar je klikt.</li>
        <li>Gebruik de knoppen <strong>+</strong> en <strong>−</strong> om verder in of uit te zoomen.</li>
        <li>Klik op het scherm-icoon om het panorama op leperello focus scherm te bekijken.</li>
        <li>Klik op het vraag-icoon om de instructie weer te zien.</li>
        <li>Klik op het Richtpunt-icoon om de hotspots aan/uit te zetten.</li>
        <li>Klik op de rode hotspots voor extra informatie.</li>
      </ul>

      <hr />

      <h3>Colofon</h3>
      <p>
        <strong>Panorama van Utrecht</strong> – titelpagina van het panorama, getekend op lithostenen
        door <strong>J. Bos</strong>, gedrukt bij <strong>P.W. van de Weijer</strong> en in juli 1859
        uitgegeven door <strong>Wed. Herfkens en Zoon</strong>.
      </p>
      <p>
        <strong>Catalogusnummer:</strong> 135001<br />
        <strong>Datering:</strong> 1859 (01-01-1859 – 31-12-1859)
      </p>
      <p>
        <strong>Auteursrecht:</strong> Publiek Domein 1.0 – u mag dit beeld downloaden, delen,
        kopiëren en bewerken, ook voor commerciële doeleinden.
      </p>
      <button class="intro-ok" type="button" @click="intro.sluit">Ik begrijp het</button>
    </div>
  </div>
</template>
