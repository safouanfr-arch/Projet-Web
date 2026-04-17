<template>
  <div id="principal-container" class="favoris-wrapper">
    <div class="container mt-4 mb-5">
      <div class="favoris-header mb-5">
        <h1>Mes favoris</h1>
        <p class="subtitle">Vos articles préférés à portée de main</p>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-large"></div>
        <p>Chargement de vos favoris...</p>
      </div>

      <!-- Message si aucun favori -->
      <div v-else-if="favoris.length === 0" class="empty-state">
        <p class="empty-icon">📦</p>
        <h3 class="empty-title">Aucun article en favoris</h3>
        <p class="empty-text">Commencez à ajouter des articles à vos favoris pour les retrouver ici.</p>
        <router-link to="/articles" class="btn btn-primary mt-3">Découvrir les articles</router-link>
      </div>

      <template v-else>
        <div class="favoris-actions">
          <p class="results-count">{{ favoris.length }} article(s) en favoris</p>
          <button class="btn btn-danger clear-btn" @click="viderFavoris">
            🗑️ Vider tous les favoris
          </button>
        </div>

        <div class="favoris-grid">
          <div v-for="item in favoris" :key="item.ident_art" class="favorite-card">
            <div class="favorite-image-wrapper">
              <img
                v-if="item.image_art"
                :src="mediaBase + item.image_art"
                :alt="item.title_art"
                class="favorite-image"
              />
              <div v-else class="favorite-image-placeholder">
                <span>📄</span>
              </div>
              <div class="favorite-overlay">
                <router-link
                  :to="{ name: 'ArticleDetail', params: { id: item.ident_art } }"
                  class="btn btn-sm btn-primary overlay-link"
                  @click="recordArticleClick"
                >
                  Lire l'article →
                </router-link>
              </div>
            </div>
            <div class="favorite-body">
              <h3 class="favorite-title">{{ item.title_art }}</h3>
              <p class="favorite-hook">{{ item.hook_art }}</p>
              <div class="favorite-meta">
                <span class="reading-time">⏱️ {{ item.readtime_art }} min</span>
              </div>
              <div class="favorite-actions">
                <router-link
                  :to="{ name: 'ArticleDetail', params: { id: item.ident_art } }"
                  class="link-read"
                  @click="recordArticleClick"
                >
                  Lire →
                </router-link>
                <button class="btn-remove" @click="retirerFavori(item.ident_art)" title="Retirer des favoris">
                  ★
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getFavorites, removeFavorite, clearFavorites } from '../api.js'
import { recordArticleClick } from '../article-ui.js'

const favoris = ref([])
const loading = ref(true)
const mediaBase = '/media/'

async function chargerFavoris() {
  try {
    const data = await getFavorites()
    if (data.success) {
      favoris.value = data.articles
    }
  } catch (e) {
    console.error('Erreur chargement favoris:', e)
  } finally {
    loading.value = false
  }
}

async function retirerFavori(id) {
  try {
    const data = await removeFavorite(id)
    if (data.success) {
      favoris.value = favoris.value.filter(item => String(item.ident_art) !== String(id))
    }
  } catch (e) {
    console.error('Erreur retrait favori:', e)
  }
}

async function viderFavoris() {
  if (confirm('Êtes-vous sûr de vouloir vider tous vos favoris ?')) {
    try {
      const data = await clearFavorites()
      if (data.success) {
        favoris.value = []
      }
    } catch (e) {
      console.error('Erreur vidage favoris:', e)
    }
  }
}

onMounted(chargerFavoris)
</script>

<style scoped>
.favoris-wrapper {
  background: #f9fafb;
  padding: 2rem 0;
  min-height: calc(100vh - 200px);
}

.favoris-header {
  text-align: center;
  padding-bottom: 2rem;
  border-bottom: 2px solid #e5e7eb;
}

.favoris-header h1 {
  color: #1f2937;
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #6b7280;
  font-size: 1.05rem;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  color: #6b7280;
}

.spinner-large {
  width: 3rem;
  height: 3rem;
  border: 3px solid #e5e7eb;
  border-top: 3px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  border: 2px dashed #e5e7eb;
  margin: 2rem 0;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  display: block;
}

.empty-title {
  color: #1f2937;
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.empty-text {
  color: #6b7280;
  font-size: 1rem;
  margin-bottom: 1.5rem;
}

.favoris-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.results-count {
  color: #6b7280;
  font-size: 1rem;
  margin: 0;
  font-weight: 500;
}

.clear-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 0.65rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.clear-btn:hover {
  background: #dc2626;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

.favoris-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.favorite-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.favorite-card:hover {
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
  transform: translateY(-8px);
  border-color: var(--primary-color);
}

.favorite-image-wrapper {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #f3f4f6;
}

.favorite-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.favorite-card:hover .favorite-image {
  transform: scale(1.05);
}

.favorite-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%);
  font-size: 3rem;
}

.favorite-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.favorite-card:hover .favorite-overlay {
  opacity: 1;
}

.overlay-link {
  background: white;
  color: var(--primary-color);
  border: none;
  padding: 0.65rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.overlay-link:hover {
  background: #f3f4f6;
  transform: translateY(-2px);
}

.favorite-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.favorite-title {
  color: #1f2937;
  font-size: 1.1rem;
  font-weight: 700;
  margin: 0 0 0.75rem 0;
  line-height: 1.3;
}

.favorite-card:hover .favorite-title {
  color: var(--primary-color);
}

.favorite-hook {
  color: #4b5563;
  font-size: 0.9rem;
  line-height: 1.5;
  margin: 0 0 1rem 0;
  flex-grow: 1;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.favorite-meta {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.reading-time {
  color: #6b7280;
  font-size: 0.85rem;
  background: #f3f4f6;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
}

.favorite-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 0.75rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.link-read {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  flex-grow: 1;
}

.link-read:hover {
  color: var(--secondary-color);
  transform: translateX(4px);
  display: inline-block;
}

.btn-remove {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #ef4444;
  padding: 0.5rem;
  border-radius: 6px;
}

.btn-remove:hover {
  background: rgba(239, 68, 68, 0.1);
  transform: scale(1.1);
}

/* Responsive */
@media (max-width: 768px) {
  .favoris-grid {
    grid-template-columns: 1fr;
  }

  .favoris-header h1 {
    font-size: 2rem;
  }

  .favoris-actions {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .clear-btn {
    width: 100%;
  }
}
</style>
