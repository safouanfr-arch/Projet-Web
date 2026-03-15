<template>
  <nav class="navbar-custom">
    <div class="container d-flex align-items-center justify-content-between">
      <!-- Logo -->
      <router-link to="/" class="navbar-logo">
        <img src="/src/image/Logo_Presse_Océan.svg.png" alt="Logo" class="logo-img">
      </router-link>

      <!-- Menu principal desktop -->
      <ul class="nav nav-menu d-none d-md-flex flex-grow-1 justify-content-center">
        <li v-for="(link, i) in links" :key="i" class="nav-item">
          <router-link :to="link.to" class="nav-link-custom"
                       @click="() => handleClick()">{{ link.text }}</router-link>
        </li>
      </ul>

      <!-- Connexion / Nom utilisateur -->
      <div class="navbar-auth d-none d-md-block">
        <span v-if="!userName" class="text-center">
          <router-link to="/Connexion" class="btn-connexion"
                       @click="() => handleClick()">Connexion</router-link>
          <small class="d-block text-muted mt-1 fs-sm">Non identifié</small>
        </span>
        <span v-else class="d-flex align-items-center gap-3">
          <span class="user-badge">
            {{ userName }}
            <span v-if="favoriteCount > 0" class="badge-fav">{{ favoriteCount }}</span>
          </span>
          <button class="btn-logout" @click="handleLogout">Déconnexion</button>
        </span>
      </div>

      <!-- Hamburger mobile -->
      <button class="btn-hamburger d-md-none" @click="() => handleClick(() => menuOpen = !menuOpen)">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

    <!-- Menu mobile -->
    <div v-if="menuOpen" class="mobile-menu d-md-none">
      <ul class="nav flex-column">
        <li v-for="(link, i) in links" :key="'m'+i" class="nav-item">
          <router-link :to="link.to" class="nav-link-mobile"
                       @click="() => handleClick()">{{ link.text }}</router-link>
        </li>
        <li class="nav-item mt-3 border-top pt-3">
          <router-link v-if="!userName" to="/Connexion" class="btn-connexion w-100 text-center d-block py-2"
                       @click="() => handleClick()">Connexion</router-link>
          <div v-else>
            <span class="user-badge d-block mb-2">Bonjour, {{ userName }}</span>
            <button class="btn-logout w-100" @click="handleLogout">Déconnexion</button>
          </div>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Bannière publicitaire (BLOC E) -->
  <div v-if="banner" class="banner-container" :style="bannerStyle">
    <a v-if="banner.url" :href="banner.url" target="_blank" rel="noopener" class="banner-link">
      <img v-if="banner.image" :src="banner.image" :alt="banner.title || 'Publicité'" class="banner-img" />
      <div class="banner-text">
        <strong v-if="banner.title">{{ banner.title }}</strong>
        <span v-if="banner.text"> — {{ banner.text }}</span>
      </div>
    </a>
    <div v-else class="banner-link">
      <img v-if="banner.image" :src="banner.image" :alt="banner.title || 'Publicité'" class="banner-img" />
      <div class="banner-text">
        <strong v-if="banner.title">{{ banner.title }}</strong>
        <span v-if="banner.text"> — {{ banner.text }}</span>
      </div>
    </div>
  </div>

  <!-- Options : police / couleur / affichage / compteur -->
  <div class="container mt-4">

    <!-- Police -->
    <div class="dropdown d-inline-block me-3">
      <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">Police</button>
      <ul class="dropdown-menu">
        <li v-for="f in fonts" :key="f.value">
          <label class="dropdown-item">
            <input type="radio" :value="f.value" v-model="selectedFont"
                   @click="() => handleClick(() => applyFont(f.value))" />
            <span :style="{ fontFamily: f.value }">{{ f.label }}</span>
          </label>
        </li>
      </ul>
    </div>

    <!-- Couleur -->
    <div class="dropdown d-inline-block me-3">
      <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">Couleur</button>
      <ul class="dropdown-menu">
        <li v-for="c in colors" :key="c.value">
          <label class="dropdown-item">
            <input type="radio" :value="c.value" v-model="selectedColor"
                   @click="() => handleClick(() => applyColor(c.value))" />
            <span :style="{ color: c.value, fontWeight: 'bold' }">{{ c.label }}</span>
          </label>
        </li>
      </ul>
    </div>

    <!-- Affichage -->
    <div class="dropdown d-inline-block me-3">
      <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">Afficher</button>
      <ul class="dropdown-menu">
        <li>
          <label class="dropdown-item">
            <input type="radio" name="display"
                   @click="() => handleClick(() => setDisplayMode('default'))"
                   :checked="displayMode==='default'" /> Par défaut
          </label>
        </li>
        <li>
          <label class="dropdown-item">
            <input type="radio" name="display"
                   @click="() => handleClick(() => setDisplayMode('image'))"
                   :checked="displayMode==='image'" /> Images
          </label>
        </li>
        <li>
          <label class="dropdown-item">
            <input type="radio" name="display"
                   @click="() => handleClick(() => setDisplayMode('text'))"
                   :checked="displayMode==='text'" /> Texte
          </label>
        </li>
      </ul>
    </div>

    <!-- Compteur -->
    <button class="btn conn-btn ms-3" @click="() => handleClick(() => clickCount = 0)">
      Click : {{ clickCount }}
    </button>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue'
import { getSession, getBanner, logout } from '../api.js'
import { clearDetailCache } from './detail.js'

// --- Données ---
const links = [
  { text: 'Accueil', to: '/' },
  { text: 'Articles', to: '/articles' },
  { text: 'Favoris', to: '/favoris' },
  { text: 'Formulaire', to: '/Formulaire' },
  { text: 'A Propos', to: '/Apropos' }
]

const fonts = [
  { label: 'Arial', value: 'Arial, sans-serif' },
  { label: 'Consolas', value: 'Consolas, monospace' },
  { label: 'Times New Roman', value: '"Times New Roman", serif' }
]

const colors = [
  { label: 'Noir', value: 'black' },
  { label: 'Bleu', value: 'blue' },
  { label: 'Rouge', value: 'red' }
]

// --- États ---
const menuOpen = ref(false)
const selectedFont = ref(localStorage.getItem('font') || 'Arial, sans-serif')
const selectedColor = ref(localStorage.getItem('color') || 'black')
const displayMode = ref(localStorage.getItem('displayMode') || 'default')
const clickCount = ref(parseInt(localStorage.getItem('clicks') || '0'))

// --- Session utilisateur (BLOC D) ---
const userName = ref(null)
const favoriteCount = ref(0)

// --- Bannière (BLOC E) ---
const banner = ref(null)

const bannerStyle = computed(() => {
  if (!banner.value) return {}
  const style = {}
  if (banner.value.background_color || banner.value.backgroundColor || banner.value.bg_color) {
    style.backgroundColor = banner.value.background_color || banner.value.backgroundColor || banner.value.bg_color
  }
  if (banner.value.color || banner.value.text_color) {
    style.color = banner.value.color || banner.value.text_color
  }
  return style
})

onMounted(async () => {
  // Charger l'état de session
  try {
    const data = await getSession()
    if (data.success && data.logged_in) {
      userName.value = data.user.name
      favoriteCount.value = data.favorite_count || 0
    }
  } catch (e) {
    console.error('Erreur session navbar:', e)
  }

  // Charger la bannière publicitaire
  try {
    const data = await getBanner()
    if (data.success && data.banner) {
      banner.value = data.banner
    }
  } catch (e) {
    console.error('Erreur bannière:', e)
  }
})

// --- Sauvegarde automatique ---
watch(selectedFont, v => { document.body.style.fontFamily = v; localStorage.setItem('font', v) }, { immediate: true })
watch(selectedColor, v => { document.body.style.color = v; localStorage.setItem('color', v) }, { immediate: true })
watch(displayMode, v => localStorage.setItem('displayMode', v))
watch(clickCount, v => localStorage.setItem('clicks', v))

function handleClick(action) {
  clickCount.value++
  if (action) action()
}

async function handleLogout() {
  try {
    await logout()
    userName.value = null
    favoriteCount.value = 0
    clearDetailCache()
    // Notifier Connexion.vue et autres composants du logout
    window.dispatchEvent(new CustomEvent('session-changed'))
  } catch (e) {
    console.error('Erreur logout:', e)
  }
}

// Synchroniser l'affichage quand la session change (login/logout depuis Connexion.vue)
// Cet event listener permet au Navbar de réagir aux changements de session sans refresh
function onSessionChanged() {
  getSession().then(data => {
    if (data.success && data.logged_in) {
      userName.value = data.user.name
      favoriteCount.value = data.favorite_count || 0
    } else {
      userName.value = null
      favoriteCount.value = 0
    }
    clearDetailCache()
  }).catch(() => {})
}

onMounted(() => {
  window.addEventListener('session-changed', onSessionChanged)
})

onUnmounted(() => {
  window.removeEventListener('session-changed', onSessionChanged)
})

function applyFont(f) {
  selectedFont.value = f
}

function applyColor(c) {
  selectedColor.value = c
}

function setDisplayMode(mode) {
  displayMode.value = mode
  const container = document.getElementById('principal-container')
  if (!container) return
  const imgs = container.querySelectorAll('img')
  const texts = container.querySelectorAll('p, span, h1, h2, h3, h4, h5, h6')
  if (mode === 'default') {
    imgs.forEach(i => i.style.display = 'block')
    texts.forEach(t => t.style.display = 'block')
  } else if (mode === 'image') {
    imgs.forEach(i => i.style.display = 'block')
    texts.forEach(t => t.style.display = 'none')
  } else if (mode === 'text') {
    imgs.forEach(i => i.style.display = 'none')
    texts.forEach(t => t.style.display = 'block')
  }
}
</script>

<style scoped>
label {
  cursor: pointer;
}

/* Navbar styling */
.navbar-custom {
  background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
  box-shadow: 0 4px 20px rgba(37, 99, 235, 0.15);
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.navbar-logo {
  flex-shrink: 0;
  transition: transform 0.3s ease;
}

.navbar-logo:hover {
  transform: scale(1.05);
}

.logo-img {
  height: 50px;
  width: auto;
  filter: brightness(1.15) saturate(1.1);
  background: rgba(255, 255, 255, 0.15);
  padding: 0.4rem 0.6rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.nav-menu {
  gap: 0.5rem;
}

.nav-link-custom {
  color: white;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: all 0.3s ease;
  position: relative;
}

.nav-link-custom:hover {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  transform: translateY(-2px);
}

.nav-link-custom::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 0;
  height: 2px;
  background: white;
  transition: all 0.3s ease;
  transform: translateX(-50%);
}

.nav-link-custom:hover::after {
  width: 80%;
}

.navbar-auth {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.btn-connexion {
  background: linear-gradient(135deg, #10b981 0%, #0891b2 100%);
  color: white;
  border: none;
  padding: 0.65rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  display: inline-block;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-connexion:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
  color: white;
}

.btn-connexion:active {
  transform: translateY(-1px);
}

.user-badge {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  padding: 0.65rem 1.25rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.95rem;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.badge-fav {
  background: #ef4444;
  color: white;
  padding: 0.2rem 0.6rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 700;
  margin-left: 0.4rem;
}

.btn-logout {
  background: #ef4444;
  color: white;
  border: none;
  padding: 0.65rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-logout:hover {
  background: #dc2626;
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

/* Mobile hamburger */
.btn-hamburger {
  display: flex;
  flex-direction: column;
  gap: 6px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.btn-hamburger span {
  width: 25px;
  height: 3px;
  background: white;
  border-radius: 2px;
  transition: all 0.3s ease;
}

.btn-hamburger:hover span {
  width: 30px;
}

/* Mobile menu */
.mobile-menu {
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(124, 58, 237, 0.95) 100%);
  padding: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.nav-link-mobile {
  color: white;
  font-weight: 500;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  transition: all 0.3s ease;
  display: block;
}

.nav-link-mobile:hover {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  padding-left: 1.5rem;
}

/* Bannière */
.banner-container {
  padding: 1rem;
  text-align: center;
  background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%);
  border-bottom: 1px solid #e0e7ff;
}

.banner-link {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s ease;
}

.banner-link:hover {
  transform: scale(1.02);
}

.banner-img {
  max-height: 70px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.banner-img:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.banner-text {
  font-size: 0.95rem;
  font-weight: 500;
  color: #1f2937;
}

/* Options section */
.dropdown {
  margin-bottom: 1rem;
}

.dropdown-toggle {
  background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.65rem 1.25rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.dropdown-toggle:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.conn-btn {
  background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
  color: white;
  border: none;
  padding: 0.65rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.conn-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.fs-sm {
  font-size: 0.85rem;
}
</style>

