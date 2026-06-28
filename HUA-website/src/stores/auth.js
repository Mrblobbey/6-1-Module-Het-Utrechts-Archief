import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { supabase, supabaseEnabled } from '@/composables/useSupabase'

const STORAGE_KEY = 'huaCmsDevUser'
const DEV_CREDENTIALS = { email: 'admin@hua.nl', password: 'admin123' }

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const isLoggedIn = computed(() => user.value !== null)
  const mode = computed(() => (supabaseEnabled ? 'supabase' : 'dev'))

  async function init() {
    if (supabaseEnabled) {
      const { data } = await supabase.auth.getSession()
      user.value = data.session?.user ?? null
      supabase.auth.onAuthStateChange((_event, session) => {
        user.value = session?.user ?? null
      })
    } else {
      const saved = localStorage.getItem(STORAGE_KEY)
      if (saved) user.value = JSON.parse(saved)
    }
  }

  async function login(email, password) {
    loading.value = true
    error.value = null

    if (!supabaseEnabled) {
      if (email === DEV_CREDENTIALS.email && password === DEV_CREDENTIALS.password) {
        const devUser = { email, id: 'dev-admin', isDev: true }
        user.value = devUser
        localStorage.setItem(STORAGE_KEY, JSON.stringify(devUser))
      } else {
        error.value = 'Onjuiste gegevens. Probeer admin@hua.nl / admin123.'
      }
      loading.value = false
      return !error.value
    }

    const { data, error: authError } = await supabase.auth.signInWithPassword({ email, password })
    if (authError) {
      error.value = authError.message
      loading.value = false
      return false
    }
    user.value = data.user
    loading.value = false
    return true
  }

  async function logout() {
    if (supabaseEnabled) {
      await supabase.auth.signOut()
    } else {
      localStorage.removeItem(STORAGE_KEY)
    }
    user.value = null
  }

  return { user, loading, error, isLoggedIn, mode, init, login, logout, devCredentials: DEV_CREDENTIALS }
})
