import { defineStore } from 'pinia'
import { ref } from 'vue'

const STORAGE_KEY = 'panoramaIntroSeen'

export const useIntroStore = defineStore('intro', () => {
  const visible = ref(localStorage.getItem(STORAGE_KEY) !== '1')

  function sluit() {
    visible.value = false
    try {
      localStorage.setItem(STORAGE_KEY, '1')
    } catch {
      // localStorage kan geblokkeerd zijn — negeren
    }
  }

  function open() {
    visible.value = true
  }

  return { visible, sluit, open }
})
