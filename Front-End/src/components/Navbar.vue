<template>
  <nav class="navbar-custom">
    <div class="container d-flex align-items-center justify-content-between">
      <router-link to="/" class="navbar-logo">
        <img src="/src/image/Logo_Presse_Océan.svg.png" alt="Logo" class="logo-img">
      </router-link>

      <ul class="nav nav-menu d-none d-md-flex flex-grow-1 justify-content-center">
        <li v-for="(link, i) in links" :key="i" class="nav-item">
          <router-link :to="link.to" class="nav-link-custom" @click="closeMenu">{{ link.text }}</router-link>
        </li>
      </ul>

      <div class="navbar-auth d-none d-md-block">
        <span v-if="!userName" class="text-center">
          <router-link to="/connexion" class="btn-connexion" @click="closeMenu">Connexion</router-link>
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

      <button class="btn-hamburger d-md-none" @click="menuOpen = !menuOpen">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

    <div v-if="menuOpen" class="mobile-menu d-md-none">
      <ul class="nav flex-column">
        <li v-for="(link, i) in links" :key="'m' + i" class="nav-item">
          <router-link :to="link.to" class="nav-link-mobile" @click="closeMenu">{{ link.text }}</router-link>
        </li>
        <li class="nav-item mt-3 border-top pt-3">
          <router-link
            v-if="!userName"
            to="/connexion"
            class="btn-connexion w-100 text-center d-block py-2"
            @click="closeMenu"
          >
            Connexion
          </router-link>
          <div v-else>
            <span class="user-badge d-block mb-2">Bonjour, {{ userName }}</span>
            <button class="btn-logout w-100" @click="handleLogout">Déconnexion</button>
          </div>
        </li>
      </ul>
    </div>
  </nav>

  <div v-if="banner" class="banner-container" :style="bannerStyle">
    <a v-if="banner.url" :href="banner.url" target="_blank" rel="noopener" class="banner-link">
      <img v-if="banner.image" :src="banner.image" :alt="banner.title || 'Publicité'" class="banner-img">
      <div class="banner-text">
        <strong v-if="banner.title">{{ banner.title }}</strong>
        <span v-if="banner.text"> - {{ banner.text }}</span>
      </div>
    </a>
    <div v-else class="banner-link">
      <img v-if="banner.image" :src="banner.image" :alt="banner.title || 'Publicité'" class="banner-img">
      <div class="banner-text">
        <strong v-if="banner.title">{{ banner.title }}</strong>
        <span v-if="banner.text"> - {{ banner.text }}</span>
      </div>
    </div>
  </div>

  <div class="container mt-4">
    <div class="options-toolbar">
      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">Police</button>
        <ul class="dropdown-menu">
          <li v-for="f in fonts" :key="f.value">
            <label class="dropdown-item">
              <input type="radio" :value="f.value" v-model="selectedFont">
              <span :style="{ fontFamily: f.value }">{{ f.label }}</span>
            </label>
          </li>
        </ul>
      </div>

      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">Couleur</button>
        <ul class="dropdown-menu">
          <li v-for="c in colors" :key="c.value">
            <label class="dropdown-item">
              <input type="radio" :value="c.value" v-model="selectedColor">
              <span :style="{ color: c.value, fontWeight: 'bold' }">{{ c.label }}</span>
            </label>
          </li>
        </ul>
      </div>

      <button class="btn conn-btn" @click="toggleMainArticlesCompact">
        {{ mainArticlesCompact ? 'Afficher images et résumés' : "N'afficher que les titres" }}
      </button>

      <div class="stats-chip">
        <span>Clics sur les liens d'articles : {{ articleLinkClickCount }}</span>
        <button class="btn btn-sm btn-outline-light" @click="resetArticleClickCount">Réinitialiser</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue'
import { getSession, getBanner, logout } from '../api.js'
import { clearDetailCache } from './detail.js'
import {
  articleLinkClickCount,
  mainArticlesCompact,
  resetArticleClickCount,
  toggleMainArticlesCompact,
} from '../article-ui.js'

const links = [
  { text: 'Accueil', to: '/' },
  { text: 'Articles', to: '/articles' },
  { text: 'Favoris', to: '/favoris' },
  { text: 'Formulaire', to: '/formulaire' },
  { text: 'A Propos', to: '/apropos' },
]

const fonts = [
  { label: 'Arial', value: 'Arial, sans-serif' },
  { label: 'Consolas', value: 'Consolas, monospace' },
  { label: 'Times New Roman', value: '"Times New Roman", serif' },
]

const colors = [
  { label: 'Noir', value: 'black' },
  { label: 'Bleu', value: 'blue' },
  { label: 'Rouge', value: 'red' },
]

const menuOpen = ref(false)
const selectedFont = ref(localStorage.getItem('font') || 'Arial, sans-serif')
const selectedColor = ref(localStorage.getItem('color') || 'black')
const userName = ref(null)
const favoriteCount = ref(0)
const banner = ref(null)

const bannerStyle = computed(() => {
  if (!banner.value) return {}
  const style = {}
  if (banner.value.background_color) {
    style.backgroundColor = banner.value.background_color
  }
  if (banner.value.color) {
    style.color = banner.value.color
  }
  return style
})

watch(selectedFont, (value) => {
  document.body.style.fontFamily = value
  localStorage.setItem('font', value)
}, { immediate: true })

watch(selectedColor, (value) => {
  document.body.style.color = value
  localStorage.setItem('color', value)
}, { immediate: true })

async function refreshSessionState() {
  try {
    const data = await getSession()
    if (data.success && data.logged_in) {
      userName.value = data.user.name
      favoriteCount.value = data.favorite_count || 0
    } else {
      userName.value = null
      favoriteCount.value = 0
    }
  } catch (e) {
    console.error('Erreur session navbar:', e)
  }
}

async function loadBanner() {
  try {
    const data = await getBanner()
    if (data.success && data.banner) {
      banner.value = data.banner
    }
  } catch (e) {
    console.error('Erreur bannière:', e)
  }
}

function closeMenu() {
  menuOpen.value = false
}

async function handleLogout() {
  try {
    await logout()
    closeMenu()
    clearDetailCache()
    window.dispatchEvent(new CustomEvent('session-changed'))
  } catch (e) {
    console.error('Erreur logout:', e)
  }
}

function onSessionChanged() {
  refreshSessionState().catch(() => {})
  clearDetailCache()
}

onMounted(async () => {
  await Promise.all([refreshSessionState(), loadBanner()])
  window.addEventListener('session-changed', onSessionChanged)
})

onUnmounted(() => {
  window.removeEventListener('session-changed', onSessionChanged)
})
</script>

<style scoped>
label {
  cursor: pointer;
}

.navbar-custom {
  background: linear-gradient(135deg, #2563eb 0%, #0f766e 100%);
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
}

.btn-logout:hover {
  background: #dc2626;
  transform: translateY(-3px);
}

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

.mobile-menu {
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(15, 118, 110, 0.95) 100%);
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

.banner-container {
  padding: 1rem;
  text-align: center;
  background: linear-gradient(135deg, #f0f4ff 0%, #ecfeff 100%);
  border-bottom: 1px solid #dbeafe;
}

.banner-link {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  text-decoration: none;
  color: inherit;
}

.banner-img {
  max-height: 70px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.banner-text {
  font-size: 0.95rem;
  font-weight: 500;
  color: #1f2937;
}

.options-toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  align-items: center;
}

.dropdown-toggle {
  background: linear-gradient(135deg, #2563eb 0%, #0f766e 100%);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.65rem 1.25rem;
  font-weight: 600;
}

.conn-btn {
  background: linear-gradient(135deg, #1d4ed8 0%, #0f766e 100%);
  color: white;
  border: none;
  padding: 0.65rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
}

.stats-chip {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border-radius: 12px;
  background: linear-gradient(135deg, #111827 0%, #374151 100%);
  color: white;
  font-weight: 600;
}

.fs-sm {
  font-size: 0.85rem;
}

@media (max-width: 768px) {
  .options-toolbar {
    align-items: stretch;
  }

  .stats-chip {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
