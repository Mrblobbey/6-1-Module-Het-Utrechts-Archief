<script setup>
import { ref } from 'vue'

const dropdownOpen = ref(null)
const searchOpen = ref(false)

const menuItems = [
  {
    key: 'onderzoek',
    label: 'Onderzoek',
    className: 'onderzoek_nav',
    items: [
      { href: 'https://hetutrechtsarchief.nl/onderzoek/collecties?view=collections', label: 'Collecties' },
      { href: 'https://hetutrechtsarchief.nl/onderzoek/zoek-op-onderwerp', label: 'Zoek op onderwerp' },
      { href: 'https://hetutrechtsarchief.nl/onderzoek/studiezaal', label: 'Studiezaal' },
      { href: 'https://hetutrechtsarchief.nl/onderzoek/bouwdossiers#', label: 'Bouwdossiers' },
      { href: 'https://hetutrechtsarchief.nl/onderzoek/archiefstuk-reserveren', label: 'Archiefstuk inzien' },
      { href: 'https://hetutrechtsarchief.nl/onderzoek/verzoek-tot-digitaliseren', label: 'Verzoek tot digitaliseren' },
      { href: 'https://hetutrechtsarchief.nl/onderzoek/help-mee', label: 'Helpt u mee?' },
      { href: 'https://hetutrechtsarchief.nl/onderzoek/open-data', label: 'Open data' },
      { href: 'https://hetutrechtsarchief.nl/onderzoek/openbaarheid-auteursrecht', label: 'Openbaarheid' },
    ],
  },
  {
    key: 'ontdekken',
    label: 'Ontdekken',
    className: 'ontdekken_dropdown',
    items: [
      { href: 'https://hetutrechtsarchief.nl/ontdekken/tentoonstellingen', label: 'Tentoonstellingen' },
      { href: 'https://hetutrechtsarchief.nl/ontdekken/agenda', label: 'Activiteiten' },
      { href: 'https://hetutrechtsarchief.nl/ontdekken/families-en-kinderen', label: 'Families en kinderen' },
      { href: 'https://hetutrechtsarchief.nl/ontdekken/plan-je-bezoek', label: 'Plan je bezoek' },
      { href: 'https://hetutrechtsarchief.nl/ontdekken/rondleidingen', label: 'Rondleidingen' },
      { href: 'https://hetutrechtsarchief.nl/ontdekken/verhalen', label: 'Verhalen' },
      { href: 'https://hetutrechtsarchief.nl/ontdekken/podcasts', label: 'Podcast' },
      { href: 'https://hetutrechtsarchief.nl/ontdekken/utrecht-time-machine', label: 'Utrecht Time Machine' },
    ],
  },
  {
    key: 'onderwijs',
    label: 'Onderwijs',
    className: 'onderwijs_dropdown',
    items: [
      { href: 'https://hetutrechtsarchief.nl/onderwijs/basis-onderwijs', label: 'Primair onderwijs' },
      { href: 'https://hetutrechtsarchief.nl/onderwijs/voortgezet-onderwijs', label: 'Voortgezet onderwijs' },
      { href: 'https://hetutrechtsarchief.nl/onderwijs/taalklassen-aanbod', label: 'Taalklassen aanbod' },
      { href: 'https://hetutrechtsarchief.nl/onderwijs/studenten', label: 'Studenten' },
      { href: 'https://hetutrechtsarchief.nl/onderwijs/cursussen', label: 'Cursussen' },
      { href: 'https://hetutrechtsarchief.nl/onderwijs/voorwaarden-groepsbezoek', label: 'Voorwaarden Groepsbezoek' },
    ],
  },
  {
    key: 'vakgenoten',
    label: 'Vakgenoten',
    className: 'vakgenoten_dropdown',
    items: [
      { href: 'https://hetutrechtsarchief.nl/vakgenoten/e-depot', label: 'e-depot' },
      { href: 'https://hetutrechtsarchief.nl/vakgenoten/archiefbeheer', label: 'Archiefbeheer' },
      { href: 'https://hetutrechtsarchief.nl/vakgenoten/toezicht', label: 'Toezicht' },
      { href: 'https://hetutrechtsarchief.nl/vakgenoten/toezicht-in-de-praktijk', label: 'Toezicht in de praktijk' },
    ],
  },
  {
    key: 'overons',
    label: 'Over ons',
    className: 'overons_dropdown',
    items: [
      { href: 'https://hetutrechtsarchief.nl/over-ons/archief-overdragen', label: 'Archief overdragen' },
      { href: 'https://hetutrechtsarchief.nl/over-ons/missie', label: 'Beleid' },
      { href: 'https://hetutrechtsarchief.nl/over-ons/projecten', label: 'Projecten' },
      { href: 'https://hetutrechtsarchief.nl/over-ons/nieuws', label: 'Nieuws' },
      { href: 'https://hetutrechtsarchief.nl/over-ons/medewerkers', label: 'Medewerkers' },
      { href: 'https://hetutrechtsarchief.nl/over-ons/vacatures', label: 'Vacatures' },
      { href: 'https://hetutrechtsarchief.nl/over-ons/word-vriend', label: 'Word vriend' },
      { href: 'https://hetutrechtsarchief.nl/over-ons/toegankelijkheid', label: 'Toegankelijkheid' },
      { href: 'https://hetutrechtsarchief.nl/over-ons/heeft-u-een-klacht', label: 'Heeft u een klacht?' },
    ],
  },
]

function toggleDropdown(key) {
  dropdownOpen.value = dropdownOpen.value === key ? null : key
}

function toggleSearch() {
  searchOpen.value = !searchOpen.value
}

function sluitDropdowns(event) {
  if (!event.target.closest('.main-nav')) {
    dropdownOpen.value = null
  }
}
</script>

<template>
  <header class="site-header" @click="sluitDropdowns">
    <div class="header-inner">
      <button
        class="search-toggle"
        :aria-expanded="searchOpen"
        aria-controls="site-search"
        @click.stop="toggleSearch"
      >
        <span v-if="!searchOpen" class="search_icon" aria-hidden="true">
          <img src="/img/search_icon.png" alt="Zoek icoon" />
        </span>
        <span v-else style="font-size: 2rem; color: white;">✕</span>
        <span class="search_openen_">Zoeken openen</span>
      </button>

      <nav class="main-nav" aria-label="Hoofdmenu">
        <ul class="main-nav__list">
          <li
            v-for="menu in menuItems"
            :key="menu.key"
            :class="[menu.className, { dropdown_open: dropdownOpen === menu.key }]"
          >
            <button
              class="main-nav__link"
              :aria-expanded="dropdownOpen === menu.key"
              @click.stop="toggleDropdown(menu.key)"
            >
              <span class="dropdown_arrow">›</span>
              <span>{{ menu.label }}</span>
            </button>
            <ul class="dropdown-menu">
              <li v-for="item in menu.items" :key="item.href">
                <a :href="item.href">{{ item.label }}</a>
              </li>
            </ul>
          </li>

          <li class="contact_nav">
            <a href="https://hetutrechtsarchief.nl/contact" class="main-nav__link main-nav__link--plain">Contact</a>
          </li>

          <li class="english_nav">
            <a href="https://hetutrechtsarchief.nl/english" class="main-nav__link main-nav__link--italic">English</a>
          </li>
        </ul>
      </nav>

      <a href="https://hetutrechtsarchief.nl/" class="site-logo">
        <img src="/img/Logo_ingeklapt.png" alt="Het Utrechts Archief Logo" />
      </a>
    </div>

    <div class="search-bar" id="site-search" :hidden="!searchOpen">
      <form action="#" method="get" class="search-bar__form" @submit.prevent>
        <input type="search" name="q" placeholder="Ik ben op zoek naar…" />
        <button type="submit">Zoeken ›</button>
      </form>
    </div>
  </header>
</template>
