<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import '@/assets/styles/cms.css'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const email = ref('')
const password = ref('')

onMounted(async () => {
  await auth.init()
  if (auth.isLoggedIn) {
    router.replace(route.query.redirect || { name: 'cms-dashboard' })
  }
})

async function submit() {
  const ok = await auth.login(email.value, password.value)
  if (ok) {
    router.replace(route.query.redirect || { name: 'cms-dashboard' })
  }
}
</script>

<template>
  <div class="cms-login">
    <div class="cms-login__card">
      <h1 class="cms-login__title">CMS inloggen</h1>
      <p class="cms-login__subtitle">Toegang tot het inhoud beheer systeem</p>

      <form @submit.prevent="submit">
        <div v-if="auth.error" class="cms-alert cms-alert--error">{{ auth.error }}</div>

        <div class="cms-field">
          <label for="email">E-mailadres</label>
          <input id="email" v-model="email" type="email" required autocomplete="username" />
        </div>

        <div class="cms-field">
          <label for="password">Wachtwoord</label>
          <input id="password" v-model="password" type="password" required autocomplete="current-password" />
        </div>

        <div class="cms-btn-row">
          <button type="submit" class="cms-btn cms-btn--primary" :disabled="auth.loading">
            {{ auth.loading ? 'Bezig…' : 'Inloggen' }}
          </button>
          <RouterLink class="cms-btn cms-btn--ghost" :to="{ name: 'home' }">← Naar site</RouterLink>
        </div>
      </form>

      <div v-if="auth.mode === 'dev'" class="cms-login__hint">
        <strong>Dev-modus actief.</strong> Supabase is nog niet geconfigureerd.
        Log in met <code>{{ auth.devCredentials.email }}</code> / <code>{{ auth.devCredentials.password }}</code>
        — wijzigingen worden alleen in je browser opgeslagen (localStorage).
      </div>
    </div>
  </div>
</template>
