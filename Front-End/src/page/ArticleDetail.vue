<template>
  <div id="principal-container" class="container mt-4">
    <div v-if="loading" class="text-center py-5">
      <p class="text-muted">Chargement de l'article...</p>
    </div>

    <div v-else-if="error" class="text-center py-5">
      <p class="text-danger">{{ error }}</p>
      <router-link to="/articles" class="btn btn-outline-primary mt-3">Retour aux articles</router-link>
    </div>

    <div v-else-if="article" class="article-detail">
      <router-link to="/articles" class="btn btn-outline-secondary mb-3">&larr; Retour aux articles</router-link>

      <!-- Visitor : resume seulement -->
      <h1 v-if="role === 'user' || role === 'admin'">{{ article.title_art }}</h1>

      <img
        v-if="article.image_art"
        :src="mediaBase + article.image_art"
        class="img-fluid rounded mb-4"
        :alt="article.title_art"
      >

      <p class="lead"><strong>{{ article.hook_art }}</strong></p>

      <div class="article-content" v-html="article.content_art"></div>

      <!-- User + Admin : date, duree de lecture, categorie -->
      <div v-if="role === 'user' || role === 'admin'" class="mt-4 p-3 bg-light rounded">
        <p class="mb-1"><strong>Date :</strong> {{ article.date_art }}</p>
        <p class="mb-1"><strong>Duree de lecture :</strong> {{ article.readtime_art }} min</p>
        <p v-if="detail && detail.category_name" class="mb-1">
          <strong>Categorie :</strong> {{ detail.category_name }}
        </p>
        <p v-if="detail && detail.word_count" class="mb-0">
          <strong>Mots :</strong> {{ detail.word_count }}
        </p>
      </div>

      <!-- Admin : infos supplementaires -->
      <div v-if="role === 'admin'" class="mt-3 p-3 border border-warning rounded bg-warning bg-opacity-10">
        <h6 class="text-warning">Infos admin</h6>
        <p class="mb-1"><strong>Titre :</strong> {{ detail.title_art }}</p>
        <p class="mb-1"><strong>Auteur :</strong> {{ detail.reporter_name }}</p>
        <p class="mb-1"><strong>ID article :</strong> {{ detail.ident_art }}</p>
        <p class="mb-0"><strong>Image :</strong> {{ detail.image_art }}</p>
      </div>

      <!-- Bannière publicitaire -->
      <div v-if="banner && banner.banner_4IPDW" class="mt-5 mb-4">
        <a :href="banner.banner_4IPDW.link" target="_blank">
          <img
            v-if="banner.banner_4IPDW.image"
            :src="banner.banner_4IPDW.image"
            :alt="banner.banner_4IPDW.text"
          >
          <p>{{ banner.banner_4IPDW.text }}</p>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { getArticleDetail, getBanner } from '../api.js'

const route = useRoute()

// État de l'article et ses détails
const article = ref(null)
const detail = ref(null)
const role = ref('visitor')
const loading = ref(true)
const error = ref('')
const banner = ref(null)
const mediaBase = '/media/'

// Charger la bannière publicitaire (asynchrone, indépendant de l'article)
async function loadBanner() {
  try {
    const data = await getBanner()
    if (data.success) {
      banner.value = data.banner
    }
  } catch (e) {
    console.error('Erreur chargement bannière:', e)
  }
}

// Charger l'article avec ses détails role-based (visitor/user/admin)
async function loadArticle(id) {
  loading.value = true
  error.value = ''
  article.value = null
  detail.value = null
  role.value = 'visitor'
  try {
    const data = await getArticleDetail(id)
    if (data.success) {
      article.value = data.article
      detail.value = data.detail
      role.value = data.role || 'visitor'
    } else {
      error.value = data.error || 'Article introuvable'
    }
  } catch (e) {
    error.value = 'Erreur de chargement'
    console.error('Erreur chargement article:', e)
  } finally {
    loading.value = false
  }
}

// Au montage: charger l'article et la bannière
onMounted(() => {
  loadArticle(route.params.id)
  loadBanner()
})

// Si l'ID change (navigation entre articles), recharger
watch(() => route.params.id, (newId) => {
  if (newId) loadArticle(newId)
})
</script>

<style scoped>
.article-detail {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.article-detail img {
  max-height: 500px;
  object-fit: cover;
  width: 100%;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  margin-bottom: 2rem;
}

.btn-outline-secondary {
  color: #6b7280;
  border: 2px solid #e5e7eb;
  background: white;
  transition: all 0.3s ease;
  padding: 0.65rem 1.25rem;
  border-radius: 8px;
  font-weight: 600;
}

.btn-outline-secondary:hover {
  color: white;
  background: #6b7280;
  border-color: #6b7280;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.article-detail h1 {
  color: #1f2937;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.article-detail .lead {
  font-size: 1.3rem;
  color: #4b5563;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%);
  border-left: 4px solid var(--primary-color);
  border-radius: 8px;
  font-style: italic;
}

.article-content {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #374151;
  margin-bottom: 2rem;
}

.article-content p {
  margin-bottom: 1.25rem;
}

.article-content h2,
.article-content h3,
.article-content h4 {
  color: #1f2937;
  margin-top: 1.5rem;
  margin-bottom: 1rem;
  font-weight: 600;
}

.article-detail .bg-light {
  background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%) !important;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.article-detail .bg-light p {
  font-size: 0.95rem;
  color: #4b5563;
  margin-bottom: 0.75rem;
}

.article-detail .bg-light strong {
  color: #1f2937;
  font-weight: 600;
}

.article-detail .border-warning {
  border: 2px solid #f59e0b !important;
  border-radius: 12px;
  background: rgba(245, 158, 11, 0.05) !important;
  padding: 1.5rem !important;
}

.article-detail .border-warning h6 {
  color: #b45309;
  font-size: 0.9rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 1rem;
}

.article-detail .border-warning p {
  color: #6b5104;
  font-size: 0.95rem;
  margin-bottom: 0.75rem;
}

.article-detail .border-warning strong {
  color: #b45309;
  font-weight: 600;
}

/* Banner styling */
.article-detail > div:last-child {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.article-detail a[href] {
  display: inline-block;
  transition: all 0.3s ease;
}

.article-detail a img {
  max-height: 200px;
  margin-bottom: 1rem;
}
</style>
