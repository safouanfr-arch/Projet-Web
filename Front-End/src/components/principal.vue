<template>
  <div class="principal-hero" v-if="articles.length >= 3">
    <div class="hero-wrapper">
      <div
        class="colonne1"
        :class="{ 'is-compact': mainArticlesCompact }"
        :style="heroStyle"
        @mouseover="loadHoverDetail(articles[0].ident_art)"
        @mouseleave="hoveredDetail = null"
        @click="goToArticle(articles[0].ident_art)"
      >
        <div class="hero-content">
          <div class="hero-top">
            <span class="hero-kicker">À la une</span>
            <BoutonFav :articleId="articles[0].ident_art" />
          </div>
          <p class="titre">{{ articles[0].title_art }}</p>
          <p v-if="!mainArticlesCompact && articles[0].hook_art" class="resume">
            {{ articles[0].hook_art }}
          </p>
        </div>
      </div>

      <div class="colonne2">
        <div
          v-for="art in articles.slice(1, 3)"
          :key="art.ident_art"
          class="sous-colonne"
          :class="{ 'is-compact': mainArticlesCompact }"
          @mouseover="loadHoverDetail(art.ident_art)"
          @mouseleave="hoveredDetail = null"
          @click="goToArticle(art.ident_art)"
        >
          <div class="carte">
            <img v-if="!mainArticlesCompact && art.image_art" :src="mediaBase + art.image_art" :alt="art.title_art">
            <div class="contenu">
              <h4>{{ art.title_art }}</h4>
              <p v-if="!mainArticlesCompact && art.hook_art" class="card-hook">{{ art.hook_art }}</p>
            </div>
            <BoutonFav :articleId="art.ident_art" />
          </div>
        </div>
      </div>
    </div>

    <ArticlePreviewPopup :payload="hoveredDetail" />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import BoutonFav from '../components/boutonFav.vue'
import ArticlePreviewPopup from './ArticlePreviewPopup.vue'
import { fetchArticleDetail } from '../components/detail.js'
import { mainArticlesCompact, recordArticleClick } from '../article-ui.js'

const router = useRouter()

const props = defineProps({
  articles: { type: Array, required: true },
})

const mediaBase = '/media/'
const hoveredDetail = ref(null)

const heroStyle = computed(() => {
  if (!mainArticlesCompact.value && props.articles.length > 0 && props.articles[0].image_art) {
    return {
      backgroundImage: `url(${mediaBase}${props.articles[0].image_art})`,
    }
  }
  return {}
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

function goToArticle(id) {
  recordArticleClick()
  router.push({ name: 'ArticleDetail', params: { id } })
}
</script>

<style scoped>
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

.colonne1 {
  border-radius: 12px;
  background-size: cover;
  background-position: center;
  min-height: 400px;
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
  inset: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.72) 0%, rgba(0, 0, 0, 0.2) 45%, transparent 100%);
}

.colonne1.is-compact {
  min-height: 220px;
  background: white;
  color: #111827;
}

.colonne1.is-compact::before {
  display: none;
}

.hero-content {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 1rem;
  height: 100%;
  padding: 1.75rem;
}

.hero-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.hero-kicker {
  display: inline-flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.16);
  backdrop-filter: blur(8px);
  border-radius: 999px;
  padding: 0.45rem 0.85rem;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.colonne1.is-compact .hero-kicker {
  background: #dbeafe;
  color: #1d4ed8;
}

.titre {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.2;
}

.resume {
  max-width: 38rem;
  margin: 0;
  font-size: 1rem;
  line-height: 1.7;
  color: rgba(255, 255, 255, 0.94);
}

.colonne1.is-compact .resume {
  display: none;
}

.colonne2 {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.sous-colonne {
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

.sous-colonne:hover,
.colonne1:hover {
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
  transform: translateY(-4px);
}

.carte {
  display: flex;
  align-items: center;
  gap: 1rem;
  width: 100%;
}

.carte img {
  width: 150px;
  height: 120px;
  border-radius: 8px;
  object-fit: cover;
  flex-shrink: 0;
}

.contenu {
  flex: 1;
  min-width: 0;
}

.contenu h4 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 700;
  color: #111827;
  line-height: 1.35;
}

.card-hook {
  margin: 0.65rem 0 0;
  color: #4b5563;
  font-size: 0.92rem;
  line-height: 1.5;
}

.sous-colonne.is-compact img,
.sous-colonne.is-compact .card-hook {
  display: none;
}

@media (max-width: 968px) {
  .hero-wrapper {
    grid-template-columns: 1fr;
  }

  .colonne1 {
    min-height: 320px;
  }
}

@media (max-width: 576px) {
  .hero-wrapper {
    padding: 1rem;
  }

  .titre {
    font-size: 1.5rem;
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
</style>
