<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useArtikelenStore } from '@/stores/artikelen'

const store = useArtikelenStore()
const router = useRouter()

const search = ref('')
const page = ref(1)
const perPage = 8
const successMsg = ref('')

onMounted(store.laden)

const filtered = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return store.sorted
  return store.sorted.filter((a) => {
    const fields = [a.catalogusnummer, a.beschrijving, a.afbeelding].filter(Boolean)
    return fields.some((v) => String(v).toLowerCase().includes(q))
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))

const paged = computed(() => {
  const start = (page.value - 1) * perPage
  return filtered.value.slice(start, start + perPage)
})

function nieuw() {
  router.push({ name: 'cms-artikel-nieuw' })
}

function bewerken(artikel) {
  router.push({ name: 'cms-artikel-edit', params: { id: artikel.id } })
}

async function verwijderen(artikel) {
  const ok = confirm(`Weet je zeker dat je artikel "${artikel.catalogusnummer}" wilt verwijderen?`)
  if (!ok) return
  const result = await store.verwijderen(artikel.id)
  if (result.success) {
    successMsg.value = `Artikel ${artikel.catalogusnummer} verwijderd.`
    setTimeout(() => (successMsg.value = ''), 2500)
  } else {
    alert(`Fout: ${result.error}`)
  }
}

function resetData() {
  if (!confirm('Alle lokale wijzigingen verwijderen en terug naar de mock-data?')) return
  store.resetNaarMock()
}
</script>

<template>
  <div v-if="successMsg" class="cms-alert cms-alert--success">{{ successMsg }}</div>
  <div v-if="store.error" class="cms-alert cms-alert--error">{{ store.error }}</div>

  <div class="cms-panel">
    <div class="cms-btn-row" style="justify-content: space-between;">
      <div class="cms-field" style="margin: 0; min-width: 260px;">
        <label for="search">Zoeken</label>
        <input id="search" v-model="search" type="search" placeholder="Catalogusnummer of beschrijving..." @input="page = 1" />
      </div>
      <div class="cms-btn-row">
        <button class="cms-btn cms-btn--ghost" @click="resetData">Reset data</button>
        <button class="cms-btn cms-btn--primary" @click="nieuw">+ Nieuw artikel</button>
      </div>
    </div>
  </div>

  <div v-if="store.loading" class="cms-loading">Artikelen laden…</div>

  <div v-else class="cms-panel" style="padding: 0;">
    <table class="cms-table">
      <thead>
        <tr>
          <th style="width: 100px;">Afbeelding</th>
          <th>Catalogus</th>
          <th>Beschrijving</th>
          <th style="width: 80px;">Hotspot</th>
          <th style="width: 180px;">Acties</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="paged.length === 0">
          <td colspan="5" class="cms-empty">Geen artikelen gevonden.</td>
        </tr>
        <tr v-for="a in paged" :key="a.id">
          <td>
            <img v-if="a.afbeelding" class="cms-thumb" :src="a.afbeelding.startsWith('data:') ? a.afbeelding : `/img/${a.afbeelding}`" alt="" />
          </td>
          <td><strong>{{ a.catalogusnummer || '—' }}</strong></td>
          <td style="max-width: 400px;">
            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
              {{ a.beschrijving || '—' }}
            </div>
          </td>
          <td>
            <span v-if="a.x !== null && a.x !== undefined" style="color: var(--red); font-weight: 600;">✓</span>
            <span v-else style="color: #ccc;">—</span>
          </td>
          <td>
            <div class="cms-btn-row">
              <button class="cms-btn cms-btn--secondary" @click="bewerken(a)">Bewerk</button>
              <button class="cms-btn cms-btn--danger" @click="verwijderen(a)">Verwijder</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="totalPages > 1" style="padding: 1rem; display: flex; gap: 0.5rem; align-items: center; justify-content: center;">
      <button class="cms-btn cms-btn--ghost" :disabled="page === 1" @click="page--">« Vorige</button>
      <span>Pagina {{ page }} / {{ totalPages }}</span>
      <button class="cms-btn cms-btn--ghost" :disabled="page === totalPages" @click="page++">Volgende »</button>
    </div>
  </div>
</template>
