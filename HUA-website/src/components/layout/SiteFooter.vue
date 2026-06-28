<script setup>
const planLinks = [
  { href: 'https://hetutrechtsarchief.nl/expo', label: 'Expo - Hamburgerstraat 28' },
  { href: 'https://hetutrechtsarchief.nl/studiezaal-en-depot', label: 'Studiezaal - Alexander Numankade 199 - 201' },
]

const onderzoekLinks = [
  { href: 'https://hetutrechtsarchief.nl/onderzoek/collecties', label: 'Archieven doorzoeken' },
  {
    href: 'https://hetutrechtsarchief.nl/beeldmateriaal-bekijken/?mode=gallery&view=horizontal&sort=random%7B1765441716711%7D%20asc',
    label: 'Beeldmateriaal bekijken',
  },
  { href: 'https://hetutrechtsarchief.nl/bouwtekeningen-en-woonomgeving', label: 'Bouwtekeningen' },
  {
    href: 'https://hetutrechtsarchief.nl/onderzoek/resultaten/personen-mais?mivast=39&mizig=100&miadt=39&miview=tbl&milang=nl',
    label: 'Personen zoeken',
  },
]

const overOnsLinks = [
  { href: 'https://hetutrechtsarchief.nl/over-ons/nieuws', label: 'Nieuws' },
  { href: 'https://hetutrechtsarchief.nl/ontdekken/agenda', label: 'Agenda' },
  { href: 'https://hetutrechtsarchief.nl/uw-materiaal-in-ons-archief', label: 'Uw materiaal in ons archief' },
  { href: 'https://hetutrechtsarchief.nl/contact', label: 'Contact' },
  { href: 'https://hetutrechtsarchief.nl/toegankelijkheid', label: 'Toegankelijkheid' },
  { href: 'https://hetutrechtsarchief.nl/disclaimer-colofon', label: 'Auteursrecht en disclaimer' },
  { href: 'https://hetutrechtsarchief.nl/privacyverklaring', label: 'Privacyverklaring' },
  { href: 'https://hetutrechtsarchief.nl/anbi', label: 'ANBI' },
]

const socialLinks = [
  { href: 'https://www.facebook.com/HetUtrechtsArchief', label: 'F', aria: 'Facebook' },
  { href: 'https://www.instagram.com/hetutrechtsarchief/', label: 'I', aria: 'Instagram' },
  { href: 'https://www.youtube.com/user/hetutrechtsarchief', label: 'Y', aria: 'YouTube' },
  { href: 'https://hetutrechtsarchief.nl/over-ons/nieuws?format=feed', label: 'RSS', aria: 'RSS' },
]
</script>

<template>
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-columns">
        <div class="footer-column">
          <h3 class="footer-title">Plan een bezoek</h3>
          <ul class="footer-linklist">
            <li v-for="link in planLinks" :key="link.href">
              <a :href="link.href">{{ link.label }}</a>
            </li>
          </ul>

          <h4 class="footer-subtitle">Onderzoek</h4>
          <ul class="footer-linklist">
            <li v-for="link in onderzoekLinks" :key="link.href">
              <a :href="link.href">{{ link.label }}</a>
            </li>
          </ul>
        </div>

        <div class="footer-column">
          <h3 class="footer-title">Over ons</h3>
          <ul class="footer-linklist">
            <li v-for="link in overOnsLinks" :key="link.href">
              <a :href="link.href">{{ link.label }}</a>
            </li>
            <li>
              <a href="https://hetutrechtsarchief.nl/english" class="footer-link-italic">English</a>
            </li>
          </ul>
        </div>

        <div class="footer-column-contact">
          <h3 class="footer-title">Contact</h3>
          <ul class="footer-contact-list">
            <li>
              <span class="footer-contact-icon">📞</span>
              <span>(030) 286 66 11</span>
            </li>
            <li>
              <span class="footer-contact-icon">@</span>
              <a href="mailto:inlichtingen@hetutrechtsarchief.nl">inlichtingen@hetutrechtsarchief.nl</a>
            </li>
            <li>
              <span class="footer-contact-icon">✉️</span>
              <span>Postadres: Postbus 131, 3500 AC Utrecht</span>
            </li>
            <li>
              <span class="footer-contact-icon">💬</span>
              <span>Chat: di t/m do 9.00 - 13.00 uur</span>
            </li>
          </ul>

          <div class="footer-social">
            <p class="footer-subtitle">Volg ons op</p>
            <div class="footer-social-links">
              <a v-for="s in socialLinks" :key="s.href" :href="s.href" :aria-label="s.aria">{{ s.label }}</a>
            </div>
          </div>
        </div>
      </div>

      <div class="footer-newsletter">
        <h3 class="footer-newsletter-title">Blijf op de hoogte van het laatste nieuws</h3>
        <form action="#" method="post" class="footer-newsletter-form" @submit.prevent>
          <input type="email" name="email" placeholder="E-mailadres" />
          <button type="submit">Verstuur</button>
        </form>
      </div>

      <div class="footer-kvk">
        <p>IBAN: NL66RABO0123881641</p>
        <p>KvK: 62047302</p>
        <p>BTW: NL807024594B01</p>
      </div>
    </div>
  </footer>
</template>
