<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useArtikelenStore } from '@/stores/artikelen'
import { useImageUpload } from '@/composables/useImageUpload'
import HotspotEditor from '@/components/cms/HotspotEditor.vue'
import PolygonEditor from '@/components/cms/PolygonEditor.vue'

const route = useRoute()
const router = useRouter()
const store = useArtikelenStore()
const { upload } = useImageUpload()

const isNew = computed(() => route.name === 'cms-artikel-nieuw')

const form = ref({
  id: null,
  catalogusnummer: '',
  beschrijving: '',
  link_bron: '',
  afbeelding: '',
  alt: '',
  x: null,
  y: null,
  polygons: null,
  height: 489,
  margin_left: 0,
  margin_top: 0,
  z_index: 0,
})

const saving = ref(false)
const uploading = ref(false)
const formMsg = ref(null)
const previewUrl = ref('')
const polygonModalIdx = ref(-1)
const polygonEditorRef = ref(null)

const imageSrcForEditor = computed(() => {
  if (previewUrl.value) return previewUrl.value
  if (!form.value.afbeelding) return ''
  return form.value.afbeelding.startsWith('data:') ? form.value.afbeelding : `/img/${form.value.afbeelding}`
})

const polygonInModal = computed(() => {
  if (polygonModalIdx.value < 0) return null
  return form.value.polygons?.[polygonModalIdx.value] ?? null
})

const hotspotLijst = computed(() => {
  const lijst = []
  if (form.value.x !== null && form.value.y !== null) {
    lijst.push({
      type: 'cirkel',
      label: 'Punt-hotspot',
      info: `(${form.value.x}, ${form.value.y})`,
    })
  }
  if (Array.isArray(form.value.polygons)) {
    form.value.polygons.forEach((p, i) => {
      lijst.push({
        type: 'polygon',
        idx: i,
        label: p.name || `Gebied ${i + 1}`,
        info: `${p.points.length} hoeken${p.beschrijving ? ' • beschr.' : ''}`,
      })
    })
  }
  return lijst
})

async function loadIfEdit() {
  if (store.artikelen.length === 0) await store.laden()
  if (isNew.value) return
  const found = store.vindArtikel(route.params.id)
  if (found) {
    const polygons = Array.isArray(found.polygons)
      ? found.polygons.map((p, i) => {
          if (Array.isArray(p)) {
            return {
              name: `Gebied ${i + 1}`,
              catalogusnummer: '',
              title: '',
              beschrijving: '',
              link_bron: '',
              points: p,
            }
          }
          return {
            name: p.name || `Gebied ${i + 1}`,
            catalogusnummer: p.catalogusnummer || '',
            title: p.title || '',
            beschrijving: p.beschrijving || '',
            link_bron: p.link_bron || '',
            points: Array.isArray(p.points) ? p.points : [],
          }
        })
      : null
    form.value = {
      ...form.value,
      ...found,
      x: found.x ?? null,
      y: found.y ?? null,
      polygons,
    }
  }
}

onMounted(loadIfEdit)
watch(() => route.params.id, loadIfEdit)

async function onFileChange(event) {
  const file = event.target.files?.[0]
  if (!file) return
  uploading.value = true
  const result = await upload(file)
  uploading.value = false
  if (!result.success) {
    formMsg.value = { type: 'error', text: result.error }
    return
  }
  form.value.afbeelding = result.url.startsWith('data:') ? result.url : result.fileName
  previewUrl.value = result.url
  if (!form.value.alt) form.value.alt = file.name
}

async function opslaan() {
  if (!form.value.catalogusnummer.trim()) {
    formMsg.value = { type: 'error', text: 'Catalogusnummer is verplicht.' }
    return
  }
  saving.value = true
  const result = await store.opslaan({ ...form.value })
  saving.value = false
  if (result.success) {
    formMsg.value = { type: 'success', text: 'Opgeslagen.' }
    setTimeout(() => router.push({ name: 'cms-dashboard' }), 800)
  } else {
    formMsg.value = { type: 'error', text: result.error }
  }
}

function annuleren() {
  router.push({ name: 'cms-dashboard' })
}

function openPolygonModal(idx) {
  polygonModalIdx.value = idx
}

function sluitModal() {
  polygonModalIdx.value = -1
}

function onPolygonClosed(idx) {
  openPolygonModal(idx)
}

function selecteerHotspotItem(item) {
  if (item.type === 'polygon') {
    polygonEditorRef.value?.selecteerPolygon(item.idx)
  }
}

function verwijderPolygon(idx) {
  if (!confirm('Polygon verwijderen?')) return
  if (Array.isArray(form.value.polygons)) {
    form.value.polygons = form.value.polygons.filter((_, i) => i !== idx)
    if (form.value.polygons.length === 0) form.value.polygons = null
  }
}

function verwijderCirkel() {
  if (!confirm('Punt-hotspot verwijderen?')) return
  form.value.x = null
  form.value.y = null
}
</script>

<template>
  <div v-if="formMsg" :class="['cms-alert', formMsg.type === 'success' ? 'cms-alert--success' : 'cms-alert--error']">
    {{ formMsg.text }}
  </div>

  <div class="cms-grid">
    <div class="cms-panel">
      <h3 class="cms-panel__title">Gegevens</h3>

      <div class="cms-field">
        <label>Catalogusnummer *</label>
        <input v-model="form.catalogusnummer" type="text" placeholder="135001" />
      </div>

      <div class="cms-field">
        <label>Beschrijving</label>
        <textarea v-model="form.beschrijving" placeholder="Korte tekst over deze locatie..."></textarea>
      </div>

      <div class="cms-field">
        <label>Bron-link</label>
        <input v-model="form.link_bron" type="text" placeholder="https://..." />
      </div>

      <div class="cms-field">
        <label>Alt-tekst (toegankelijkheid)</label>
        <input v-model="form.alt" type="text" placeholder="Beschrijving voor screenreaders" />
      </div>

      <div class="cms-field">
        <label>Afbeelding (jpg/png)</label>
        <input type="file" accept="image/*" :disabled="uploading" @change="onFileChange" />
        <small v-if="uploading" style="color: var(--green);">Bezig met uploaden…</small>
        <small v-else-if="form.afbeelding" style="color: #666;">Huidige: {{ form.afbeelding.startsWith('data:') ? '(net geüpload)' : form.afbeelding }}</small>
      </div>

      <div class="cms-btn-row">
        <button class="cms-btn cms-btn--primary" :disabled="saving" @click="opslaan">
          {{ saving ? 'Opslaan…' : 'Opslaan' }}
        </button>
        <button class="cms-btn cms-btn--ghost" @click="annuleren">Annuleren</button>
      </div>
    </div>

    <div class="cms-panel">
      <h3 class="cms-panel__title">Hotspots in dit artikel</h3>
      <div v-if="hotspotLijst.length === 0" class="cms-empty">
        Nog geen hotspots. Voeg ze hieronder toe.
      </div>
      <ul v-else class="hotspots-list">
        <li v-for="(item, i) in hotspotLijst" :key="i" class="hotspots-list__item">
          <span class="hotspots-list__type" :class="`hotspots-list__type--${item.type}`">
            {{ item.type === 'cirkel' ? '●' : '◆' }}
          </span>
          <button
            v-if="item.type === 'polygon'"
            type="button"
            class="hotspots-list__label"
            @click="selecteerHotspotItem(item)"
          >
            {{ item.label }}
          </button>
          <span v-else class="hotspots-list__label">{{ item.label }}</span>
          <span class="hotspots-list__info">{{ item.info }}</span>
          <button
            v-if="item.type === 'polygon'"
            type="button"
            class="hotspots-list__action"
            @click="openPolygonModal(item.idx)"
            title="Bewerk info"
          >
            ✎
          </button>
          <button
            type="button"
            class="hotspots-list__action hotspots-list__action--danger"
            @click="item.type === 'cirkel' ? verwijderCirkel() : verwijderPolygon(item.idx)"
            title="Verwijder"
          >
            ×
          </button>
        </li>
      </ul>

      <h4 style="margin-top: 1.5rem; font-size: 0.95rem; color: var(--green);">Punt-hotspot (rond)</h4>
      <p style="color: #666; font-size: 0.85rem; margin: 0 0 0.5rem;">
        Sleep de rode bal naar de juiste plek.
      </p>
      <div v-if="!imageSrcForEditor" class="cms-empty">
        Kies eerst een afbeelding hierboven.
      </div>
      <HotspotEditor
        v-else
        :image-src="imageSrcForEditor"
        :model-value-x="form.x"
        :model-value-y="form.y"
        @update:model-value-x="form.x = $event"
        @update:model-value-y="form.y = $event"
      />
    </div>
  </div>

  <div class="cms-panel" style="margin-top: 1.5rem;">
    <h3 class="cms-panel__title">Gebied-hotspots (polygons)</h3>
    <p style="color: #666; font-size: 0.9rem; margin-top: 0;">
      Klik = hoekpunt toevoegen. Dubbelklik op afbeelding = sluiten + info-pop-up.
      Scrollwiel = inzoomen voor detailwerk.
    </p>

    <div v-if="!imageSrcForEditor" class="cms-empty">
      Kies eerst een afbeelding hierboven.
    </div>
    <PolygonEditor
      v-else
      ref="polygonEditorRef"
      :image-src="imageSrcForEditor"
      :model-value="form.polygons"
      @update:model-value="form.polygons = $event"
      @polygon-closed="onPolygonClosed"
      @polygon-bewerken="openPolygonModal"
    />
  </div>

  <div v-if="polygonInModal" class="polygon-modal">
    <div class="polygon-modal__card">
      <button type="button" class="polygon-modal__close" aria-label="Sluiten" @click="sluitModal">×</button>
      <h3>Info voor gebied #{{ polygonModalIdx + 1 }}</h3>
      <p style="color: #666; font-size: 0.9rem; margin-top: 0;">
        Vul de gegevens in voor dit gebied. Leeg laten gebruikt de info van het artikel.
      </p>

      <div class="cms-field">
        <label>Naam (interne label in lijst) *</label>
        <input v-model="polygonInModal.name" type="text" placeholder="Bijv. Domtoren, Janskerk..." />
      </div>

      <div class="cms-field">
        <label>Catalogusnummer</label>
        <input v-model="polygonInModal.catalogusnummer" type="text" placeholder="135001" />
      </div>

      <div class="cms-field">
        <label>Title (toont op hoofdpagina)</label>
        <input v-model="polygonInModal.title" type="text" placeholder="Bijv. Domtoren met Buurkerk" />
      </div>

      <div class="cms-field">
        <label>Beschrijving</label>
        <textarea v-model="polygonInModal.beschrijving" placeholder="Korte tekst over dit gebouw..."></textarea>
      </div>

      <div class="cms-field">
        <label>Bron-link</label>
        <input v-model="polygonInModal.link_bron" type="text" placeholder="https://..." />
      </div>

      <div class="cms-btn-row">
        <button class="cms-btn cms-btn--primary" @click="sluitModal">Klaar</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hotspots-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.hotspots-list__item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.7rem;
  background: #fafafa;
  border: 1px solid #eee;
  border-radius: 4px;
  font-size: 0.9rem;
}

.hotspots-list__type {
  width: 22px;
  height: 22px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-size: 0.95rem;
}

.hotspots-list__type--cirkel {
  background: rgba(228, 68, 33, 0.15);
  color: var(--red);
}

.hotspots-list__type--polygon {
  background: rgba(22, 106, 90, 0.15);
  color: var(--green);
}

.hotspots-list__label {
  background: transparent;
  border: none;
  padding: 0;
  font-family: inherit;
  font-weight: 600;
  color: var(--black);
  cursor: pointer;
  text-align: left;
}

button.hotspots-list__label:hover {
  text-decoration: underline;
}

.hotspots-list__info {
  margin-left: auto;
  font-size: 0.8rem;
  color: #666;
}

.hotspots-list__action {
  background: #fff;
  border: 1px solid #ccc;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  cursor: pointer;
  font-family: inherit;
  font-size: 0.9rem;
}

.hotspots-list__action:hover {
  background: #f5f5f5;
}

.hotspots-list__action--danger:hover {
  background: var(--red);
  color: #fff;
  border-color: var(--red);
}

.polygon-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9000;
  padding: 1rem;
}

.polygon-modal__card {
  position: relative;
  background: #fff;
  padding: 1.75rem 2rem;
  border-radius: 8px;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.polygon-modal__card h3 {
  margin-top: 0;
  color: var(--green);
  padding-right: 2rem;
}

.polygon-modal__close {
  position: absolute;
  top: 0.5rem;
  right: 0.7rem;
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  font-size: 1.6rem;
  line-height: 1;
  color: #666;
  cursor: pointer;
  border-radius: 4px;
}

.polygon-modal__close:hover {
  background: #f5f5f5;
  color: var(--red);
}
</style>
