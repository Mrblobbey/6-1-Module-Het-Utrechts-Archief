import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { supabase, supabaseEnabled } from '@/composables/useSupabase'
import { artikelenMock } from '@/data/artikelenMock'

const STORAGE_KEY = 'huaCmsLocalArtikelen'
const CHANNEL_NAME = 'hua-artikelen-sync'

function migreer(artikel) {
  if (artikel.polygon && !artikel.polygons) {
    artikel.polygons = [artikel.polygon]
    delete artikel.polygon
  }
  if (Array.isArray(artikel.polygons)) {
    artikel.polygons = artikel.polygons.map((p, i) => {
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
  }
  return artikel
}

function loadLocal() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (raw) return JSON.parse(raw).map(migreer)
  } catch {}
  return JSON.parse(JSON.stringify(artikelenMock))
}

function saveLocal(list) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(list))
  } catch {}
}

const channel =
  typeof window !== 'undefined' && 'BroadcastChannel' in window
    ? new BroadcastChannel(CHANNEL_NAME)
    : null

export const useArtikelenStore = defineStore('artikelen', () => {
  const artikelen = ref([])
  const loading = ref(false)
  const error = ref(null)

  const sorted = computed(() =>
    [...artikelen.value].sort((a, b) => (a.id ?? 0) - (b.id ?? 0)),
  )

  if (typeof window !== 'undefined' && !supabaseEnabled) {
    window.addEventListener('storage', (event) => {
      if (event.key !== STORAGE_KEY) return
      artikelen.value = event.newValue ? JSON.parse(event.newValue) : []
    })
    if (channel) {
      channel.addEventListener('message', (event) => {
        if (event.data?.type === 'artikelen-updated' && Array.isArray(event.data.list)) {
          artikelen.value = event.data.list
        }
      })
    }
  }

  function broadcastUpdate() {
    if (!channel) return
    try {
      channel.postMessage({
        type: 'artikelen-updated',
        list: JSON.parse(JSON.stringify(artikelen.value)),
      })
    } catch (err) {
      console.warn('BroadcastChannel postMessage faalde:', err)
    }
  }

  async function laden() {
    loading.value = true
    error.value = null

    if (!supabaseEnabled) {
      artikelen.value = loadLocal()
      loading.value = false
      return
    }

    const { data, error: dbError } = await supabase
      .from('artikel')
      .select('*')
      .order('id', { ascending: true })

    if (dbError) {
      error.value = dbError.message
      artikelen.value = loadLocal()
    } else {
      artikelen.value = data ?? []
    }
    loading.value = false
  }

  function vindArtikel(id) {
    return artikelen.value.find((a) => String(a.id) === String(id)) ?? null
  }

  async function opslaan(partial) {
    if (!supabaseEnabled) {
      const idx = artikelen.value.findIndex((a) => String(a.id) === String(partial.id))
      if (idx === -1) {
        const nieuwId = Math.max(0, ...artikelen.value.map((a) => Number(a.id) || 0)) + 1
        const nieuw = { ...partial, id: nieuwId }
        artikelen.value = [...artikelen.value, nieuw]
      } else {
        const updated = [...artikelen.value]
        updated[idx] = { ...updated[idx], ...partial }
        artikelen.value = updated
      }
      saveLocal(artikelen.value)
      broadcastUpdate()
      return { success: true }
    }

    const { id, ...rest } = partial
    if (id == null || id === '') {
      const { data, error: insertError } = await supabase.from('artikel').insert(rest).select().single()
      if (insertError) return { success: false, error: insertError.message }
      artikelen.value = [...artikelen.value, data]
      return { success: true, data }
    }
    const { data, error: updateError } = await supabase
      .from('artikel')
      .update(rest)
      .eq('id', id)
      .select()
      .single()
    if (updateError) return { success: false, error: updateError.message }
    const idx = artikelen.value.findIndex((a) => String(a.id) === String(id))
    if (idx !== -1) {
      const updated = [...artikelen.value]
      updated[idx] = data
      artikelen.value = updated
    }
    return { success: true, data }
  }

  async function verwijderen(id) {
    if (!supabaseEnabled) {
      artikelen.value = artikelen.value.filter((a) => String(a.id) !== String(id))
      saveLocal(artikelen.value)
      broadcastUpdate()
      return { success: true }
    }
    const { error: delError } = await supabase.from('artikel').delete().eq('id', id)
    if (delError) return { success: false, error: delError.message }
    artikelen.value = artikelen.value.filter((a) => String(a.id) !== String(id))
    return { success: true }
  }

  async function bulkLayoutOpslaan(updates) {
    if (!supabaseEnabled) {
      const map = new Map(updates.map((u) => [String(u.id), u]))
      artikelen.value = artikelen.value.map((a) =>
        map.has(String(a.id)) ? { ...a, ...map.get(String(a.id)) } : a,
      )
      saveLocal(artikelen.value)
      broadcastUpdate()
      return { success: true }
    }
    const results = await Promise.all(
      updates.map((u) =>
        supabase
          .from('artikel')
          .update({ margin_left: u.margin_left, margin_top: u.margin_top, z_index: u.z_index })
          .eq('id', u.id),
      ),
    )
    const failed = results.find((r) => r.error)
    if (failed) return { success: false, error: failed.error.message }
    await laden()
    return { success: true }
  }

  function resetNaarMock() {
    localStorage.removeItem(STORAGE_KEY)
    artikelen.value = JSON.parse(JSON.stringify(artikelenMock))
  }

  return {
    artikelen,
    sorted,
    loading,
    error,
    laden,
    vindArtikel,
    opslaan,
    verwijderen,
    bulkLayoutOpslaan,
    resetNaarMock,
  }
})
