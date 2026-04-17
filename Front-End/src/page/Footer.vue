<template>
  <footer class="footer">
    <div class="container py-5">
      <div class="row">
        <div class="col-md-4 mb-4 mb-md-0">
          <div class="footer-brand">
            <img src="/src/image/Logo_Presse_Océan.svg.png" alt="Logo" class="footer-logo">
            <span class="footer-brand-text">Presse Océan</span>
          </div>
          <p class="footer-description">
            Votre source d'information en ligne pour tous les articles et actualités.
          </p>
        </div>

        <div class="col-md-4 mb-4 mb-md-0">
          <h6 class="footer-section-title">Liens rapides</h6>
          <ul class="footer-links">
            <li v-for="(link, i) in links" :key="i">
              <router-link v-if="link.to" :to="link.to" class="footer-link">
                <span class="link-icon">→</span>
                {{ link.text }}
              </router-link>
              <a v-else :href="link.href" class="footer-link">
                <span class="link-icon">→</span>
                {{ link.text }}
              </a>
            </li>
          </ul>
        </div>

        <div class="col-md-4">
          <h6 class="footer-section-title">Informations</h6>
          <ul class="footer-info">
            <li>
              <span class="info-label">Email :</span>
              <a href="mailto:info@presseocean.com" class="footer-link">info@presseocean.com</a>
            </li>
            <li>
              <span class="info-label">Téléphone :</span>
              <a href="tel:+33123456789" class="footer-link">+33 1 23 45 67 89</a>
            </li>
          </ul>
        </div>
      </div>

      <div class="col-md-4 articles-by-date">
        <h6 class="footer-section-title">Vérifier</h6>
        <div class="date-search">
          <input v-model="date" type="date" class="date-input">
          <button class="date-button" @click="search">Vérifier</button>
          <span v-if="result !== null" class="date-result">{{ result }} article(s)</span>
        </div>
      </div>

      <div class="footer-bottom">
        <div class="footer-divider"></div>
        <div class="footer-content-bottom">
          <p class="footer-copyright">© 2026 Presse Océan. Tous droits réservés.</p>
          <div class="footer-credit">
            <span>Développé pour le projet ISFCE</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'
import { getArticlesByDate } from '../api.js'

const links = [
  { text: 'Accueil', to: '/' },
  { text: 'Articles', to: '/articles' },
  { text: 'Favoris', to: '/favoris' },
  { text: 'Recherche', to: '/formulaire' },
  { text: 'A Propos', to: '/apropos' },
]

const date = ref('')
const result = ref(null)

async function search() {
  if (!date.value) return

  try {
    const data = await getArticlesByDate(date.value)
    result.value = data.success ? data.count : 0
  } catch (e) {
    console.error('Erreur verification date:', e)
    result.value = 0
  }
}
</script>

<style scoped>
.footer {
  background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
  color: #e5e7eb;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  margin-top: auto;
  padding: 2rem 0 0 0;
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.footer-logo {
  height: 45px;
  width: auto;
  filter: brightness(1.3) saturate(1.2);
  transition: transform 0.3s ease;
}

.footer-brand:hover .footer-logo {
  transform: scale(1.05);
}

.footer-brand-text {
  font-size: 1.25rem;
  font-weight: 700;
  color: white;
  letter-spacing: 0.5px;
}

.footer-description {
  color: #9ca3af;
  font-size: 0.95rem;
  line-height: 1.6;
  margin: 0;
}

.footer-section-title {
  color: white;
  font-size: 0.9rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 1.25rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid rgba(156, 163, 175, 0.3);
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.75rem;
}

.footer-link {
  color: #d1d5db;
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.footer-link:hover {
  color: white;
  padding-left: 0.5rem;
}

.link-icon {
  opacity: 0;
  transition: opacity 0.3s ease;
  font-weight: bold;
}

.footer-link:hover .link-icon {
  opacity: 1;
}

.footer-info {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-info li {
  margin-bottom: 0.75rem;
  font-size: 0.95rem;
}

.info-label {
  color: #9ca3af;
  font-weight: 600;
  display: block;
  margin-bottom: 0.25rem;
}

.footer-bottom {
  margin-top: 2rem;
  padding-top: 2rem;
}

.footer-divider {
  height: 1px;
  background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
  margin-bottom: 1.5rem;
}

.footer-content-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  font-size: 0.85rem;
}

.footer-copyright {
  color: #9ca3af;
  margin: 0;
}

.footer-credit {
  color: #6b7280;
  font-style: italic;
}

.articles-by-date {
  margin-top: 1.5rem;
}

.date-search {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  flex-wrap: wrap;
}

.date-input {
  padding: 0.5rem;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 4px;
  color: white;
  font-size: 0.9rem;
}

.date-button {
  padding: 0.5rem 1rem;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 600;
}

.date-result {
  color: #10b981;
  font-weight: 600;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .footer {
    padding: 2rem 0 1rem 0;
  }

  .footer-content-bottom {
    flex-direction: column;
    align-items: flex-start;
    text-align: center;
  }

  .footer-section-title {
    margin-top: 1.5rem;
  }

  .footer-section-title:first-of-type {
    margin-top: 0;
  }
}
</style>
