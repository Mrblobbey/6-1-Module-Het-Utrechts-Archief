import { ref } from 'vue'
import { supabase, supabaseEnabled } from './useSupabase'
import { artikelenMock } from '@/data/artikelenMock'

export function useArtikelen() {
  const artikelen = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function laden() {
    loading.value = true
    error.value = null

    if (!supabaseEnabled) {
      artikelen.value = artikelenMock
      loading.value = false
      return
    }

    const { data, error: dbError } = await supabase
      .from('artikel')
      .select('*')
      .order('id', { ascending: true })

    if (dbError) {
      error.value = dbError.message
      artikelen.value = artikelenMock
    } else {
      artikelen.value = data ?? []
    }

    loading.value = false
  }

  return { artikelen, loading, error, laden }
}
