<template>
  <div class="principal-hero" v-if="articles.length >= 3">
    <div class="hero-wrapper">
      <!-- Colonne principale avec image de fond -->
      <div class="colonne1" :style="heroStyle"
           @mouseover="loadHoverDetail(articles[0].ident_art)"
           @mouseleave="hoveredDetail = null"
           @click="goToArticle(articles[0].ident_art)">
        <p class="titre">
          {{ articles[0].title_art }}
        </p>
      </div>

      <!-- Colonne secondaire avec cartes -->
      <div class="colonne2">
        <div class="sous-colonne" v-for="art in articles.slice(1, 3)" :key="art.ident_art"
             @mouseover="loadHoverDetail(art.ident_art)"
             @mouseleave="hoveredDetail = null"
             @click="goToArticle(art.ident_art)">
          <div class="carte">
            <img :src="mediaBase + art.image_art" :alt="art.title_art" />
            <div class="contenu">
              <h4>{{ art.title_art }}</h4>
            </div>
            <BoutonFav :articleId="art.ident_art" />
          </div>
        </div>
      </div>
    </div>

    <!-- Fenetre d'apercu au survol (chargee via fetch async depuis la BDD) -->
    <div v-if="hoveredDetail" class="preview-popup">
      <p><em>Date : {{ hoveredDetail.detail.date_art }}</em></p>
      <p><em>Temps de lecture : {{ hoveredDetail.detail.readtime_art }} min</em></p>
      <p v-if="hoveredDetail.detail.word_count"><em>Mots : {{ hoveredDetail.detail.word_count }}</em></p>
      <p>{{ hoveredDetail.detail.hook_art }}</p>
      <!-- user/admin : categorie -->
      <p v-if="hoveredDetail.detail.category_name">
        <strong>Categorie :</strong> {{ hoveredDetail.detail.category_name }}
      </p>
      <!-- admin : infos supplementaires -->
      <template v-if="hoveredDetail.role === 'admin'">
        <p><strong>Titre :</strong> {{ hoveredDetail.detail.title_art }}</p>
        <p><strong>Auteur :</strong> {{ hoveredDetail.detail.reporter_name }}</p>
        <p><strong>ID :</strong> {{ hoveredDetail.detail.ident_art }}</p>
        <p><strong>Image :</strong> {{ hoveredDetail.detail.image_art }}</p>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import BoutonFav from '../components/boutonFav.vue'
import { fetchArticleDetail } from '../components/detail.js'

const router = useRouter()

const props = defineProps({
  articles: { type: Array, required: true }
})

const mediaBase = '/media/'
const hoveredDetail = ref(null)

const heroStyle = computed(() => {
  if (props.articles.length > 0 && props.articles[0].image_art) {
    return {
      backgroundImage: `url(${mediaBase}${props.articles[0].image_art})`
    }
  }
  return {}
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

function goToArticle(id) {
  router.push({ name: 'ArticleDetail', params: { id } })
}
</script>

<style scoped>
/* Principal Hero Container */
.principal-hero {
  width: 100%;
}

.hero-wrapper {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
  background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
  padding: 2rem;
  border-radius: 12px;
  align-items: stretch;
}

/* Colonne 1 : image de fond avec texte */
.colonne1 {
  border-radius: 12px;
  background-size: cover;
  background-position: center;
  height: 400px;
  position: relative;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  overflow: hidden;
}

.colonne1::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.6) 0%, transparent 50%);
  z-index: 1;
}

.colonne1:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
}

.titre {
  position: absolute;
  bottom: 2rem;
  left: 2rem;
  right: 2rem;
  z-index: 2;
  color: white;
  font-size: 28px;
  font-weight: 700;
  line-height: 1.3;
  max-width: 100%;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

/* Colonne 2 : cartes secondaires */
.colonne2 {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.sous-colonne {
  border: none;
  border-radius: 12px;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid #e5e7eb;
  min-height: 180px;
  display: flex;
  align-items: center;
}

.sous-colonne:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
  border-color: #2563eb;
}

.carte {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  width: 100%;
}

.carte img {
  width: 160px;
  height: 130px;
  border-radius: 8px;
  object-fit: cover;
  flex-shrink: 0;
}

.carte .contenu {
  flex: 1;
  min-width: 0;
}

.carte .contenu h4 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.sous-colonne:hover .carte .contenu h4 {
  color: #2563eb;
}

/* Preview popup */
.preview-popup {
  position: fixed;
  top: 100px;
  right: 30px;
  width: 280px;
  padding: 1.5rem;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  z-index: 100;
  animation: slideInRight 0.3s ease;
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

.preview-popup p {
  font-size: 0.85rem;
  color: #4b5563;
  margin-bottom: 0.75rem;
}

/* Responsive */
@media (max-width: 968px) {
  .hero-wrapper {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .colonne1 {
    height: 300px;
  }

  .titre {
    font-size: 22px;
    bottom: 1.5rem;
    left: 1.5rem;
    right: 1.5rem;
  }

  .carte {
    flex-direction: column;
    align-items: flex-start;
  }

  .carte img {
    width: 100%;
    height: auto;
  }
}

@media (max-width: 576px) {
  .hero-wrapper {
    padding: 1rem;
  }

  .colonne1 {
    height: 250px;
  }

  .titre {
    font-size: 18px;
    bottom: 1rem;
    left: 1rem;
    right: 1rem;
  }

  .preview-popup {
    display: none;
  }
}
</style>
