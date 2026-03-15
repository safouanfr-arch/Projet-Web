<template>
  <div id="principal-container" class="container mt-4 mb-5">
    <div class="articles-header mb-5">
      <h1>Tous les articles</h1>
      <p class="text-muted subtitle" v-if="articles.length">{{ articles.length }} article(s) disponible(s)</p>
      <p class="text-muted subtitle" v-else>Chargement des articles...</p>
    </div>

    <div class="row">
      <!-- Liste des articles en grille -->
      <div class="col-12">
        <section class="articles-grid">
          <article
            v-for="art in articles"
            :key="art.ident_art"
            class="article-card"
            @mouseover="loadHoverDetail(art.ident_art)"
            @mouseleave="hoveredDetail = null"
            @click="loadDetail(art.ident_art)"
          >
            <div class="article-card-inner">
              <div class="article-meta">
                <span class="reading-time">{{ art.readtime_art }} min de lecture</span>
              </div>
              <h2 class="article-title">{{ art.title_art }}</h2>
              <p class="article-hook">{{ art.hook_art }}</p>
              <div class="article-footer">
                <span class="read-more">Lire l'article →</span>
              </div>
            </div>
          </article>
        </section>
      </div>
    </div>

    <!-- Fenetre d'apercu au survol (chargee via fetch async depuis la BDD) -->
    <div v-if="hoveredDetail" class="preview-popup">
      <div class="preview-header">
        <h3>Aperçu</h3>
      </div>
      <div class="preview-content">
        <p><strong>Date :</strong> <em>{{ hoveredDetail.detail.date_art }}</em></p>
        <p><strong>Temps de lecture :</strong> <em>{{ hoveredDetail.detail.readtime_art }} min</em></p>
        <p v-if="hoveredDetail.detail.word_count"><strong>Mots :</strong> <em>{{ hoveredDetail.detail.word_count }}</em></p>
        <p class="hook-preview">{{ hoveredDetail.detail.hook_art }}</p>
        <!-- user/admin : categorie -->
        <p v-if="hoveredDetail.detail.category_name">
          <strong>Catégorie :</strong> <span class="category-badge">{{ hoveredDetail.detail.category_name }}</span>
        </p>
        <!-- admin : infos supplementaires -->
        <template v-if="hoveredDetail.role === 'admin'">
          <div class="admin-info">
            <p class="admin-label">📊 Infos Admin</p>
            <p><strong>Titre :</strong> {{ hoveredDetail.detail.title_art }}</p>
            <p><strong>Auteur :</strong> {{ hoveredDetail.detail.reporter_name }}</p>
            <p><strong>ID :</strong> {{ hoveredDetail.detail.ident_art }}</p>
            <p><strong>Image :</strong> {{ hoveredDetail.detail.image_art }}</p>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getArticles } from '../api.js'
import { fetchArticleDetail } from '../components/detail.js'

const router = useRouter()
const articles = ref([])
const hoveredDetail = ref(null)
const mediaBase = '/media/'

onMounted(async () => {
  try {
    const data = await getArticles()
    if (data.success) {
      articles.value = data.articles
    }
  } catch (e) {
    console.error('Erreur chargement articles:', e)
  }
})

// Survol : requete asynchrone via detail.js (fetch) vers detail_fetch.php
async function loadHoverDetail(id) {
  try {
    const data = await fetchArticleDetail(id)
    if (data.success) {
      hoveredDetail.value = data
    }
  } catch (e) {
    console.error('Erreur chargement detail survol:', e)
  }
}

// Clic : naviguer vers la page de detail
function loadDetail(id) {
  router.push({ name: 'ArticleDetail', params: { id } })
}
</script>

<style scoped>
.articles-header {
  text-align: center;
  padding: 1rem 0;
  border-bottom: 2px solid #e5e7eb;
}

.articles-header h1 {
  color: #1f2937;
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.articles-header .subtitle {
  font-size: 1.05rem;
  color: #6b7280;
}

.articles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.article-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: all 0.3s ease;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

.article-card:hover {
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
  transform: translateY(-8px);
  border-color: var(--primary-color);
}

.article-card-inner {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.article-meta {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 0.85rem;
}

.reading-time {
  color: #6b7280;
  font-weight: 500;
  background: #f3f4f6;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
}

.article-title {
  color: #1f2937;
  font-size: 1.4rem;
  font-weight: 700;
  margin: 1rem 0;
  line-height: 1.3;
  flex-grow: 1;
}

.article-card:hover .article-title {
  color: var(--primary-color);
}

.article-hook {
  color: #4b5563;
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 1rem;
  flex-grow: 1;
}

.article-footer {
  display: flex;
  justify-content: flex-end;
}

.read-more {
  color: var(--primary-color);
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
}

.article-card:hover .read-more {
  transform: translateX(4px);
  color: var(--secondary-color);
}

/* Preview popup styling */
.preview-popup {
  position: fixed;
  top: 120px;
  right: 2rem;
  width: 320px;
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  z-index: 100;
  animation: slideInRight 0.3s ease;
  overflow: hidden;
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.preview-header {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  color: white;
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.preview-header h3 {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
}

.preview-content {
  padding: 1.25rem;
  max-height: 60vh;
  overflow-y: auto;
}

.preview-content p {
  font-size: 0.9rem;
  margin-bottom: 0.75rem;
  color: #4b5563;
}

.preview-content strong {
  color: #1f2937;
  font-weight: 600;
}

.hook-preview {
  background: #f9fafb;
  padding: 0.75rem;
  border-left: 3px solid var(--primary-color);
  border-radius: 4px;
  font-style: italic;
  margin: 1rem 0 !important;
}

.category-badge {
  display: inline-block;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.85rem;
}

.admin-info {
  background: rgba(245, 158, 11, 0.05);
  border: 1px solid #fcd34d;
  border-radius: 6px;
  padding: 0.75rem;
  margin-top: 1rem;
}

.admin-label {
  color: #b45309;
  font-weight: 700;
  margin-bottom: 0.5rem !important;
  font-size: 0.85rem;
}

.admin-info p {
  font-size: 0.85rem;
  color: #6b5104;
  margin-bottom: 0.5rem;
}

.admin-info strong {
  color: #b45309;
}

/* Responsive */
@media (max-width: 768px) {
  .articles-grid {
    grid-template-columns: 1fr;
  }

  .preview-popup {
    position: fixed;
    top: auto;
    bottom: 1rem;
    right: 1rem;
    left: 1rem;
    width: auto;
  }

  .articles-header h1 {
    font-size: 2rem;
  }
}
</style>
