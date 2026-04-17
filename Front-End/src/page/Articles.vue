<template>
  <div id="principal-container" class="container mt-4 mb-5">
    <div class="articles-header mb-5">
      <h1>Tous les articles</h1>
      <p v-if="articles.length" class="text-muted subtitle">{{ articles.length }} article(s) disponible(s)</p>
      <p v-else class="text-muted subtitle">Chargement des articles...</p>
    </div>

    <div class="row">
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

    <ArticlePreviewPopup :payload="hoveredDetail" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getArticles } from '../api.js'
import { fetchArticleDetail } from '../components/detail.js'
import ArticlePreviewPopup from '../components/ArticlePreviewPopup.vue'
import { recordArticleClick } from '../article-ui.js'

const router = useRouter()
const articles = ref([])
const hoveredDetail = ref(null)

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

function loadDetail(id) {
  recordArticleClick()
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

@media (max-width: 768px) {
  .articles-grid {
    grid-template-columns: 1fr;
  }

  .articles-header h1 {
    font-size: 2rem;
  }
}
</style>
