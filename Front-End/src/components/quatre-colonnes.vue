<template>
  <div class="container mt-4">
    <div class="row g-4" v-if="articles.length >= 8">
      <div v-for="(col, ci) in [[0, 1], [2, 3], [4, 5], [6, 7]]" :key="ci" class="col-md-3">
        <div class="card-principale p-3">
          <div class="sous-cartes mt-3">
            <div
              v-for="i in col"
              :key="articles[i].ident_art"
              class="carte-interne"
              @mouseover="loadHoverDetail(articles[i].ident_art)"
              @mouseleave="hoveredDetail = null"
              @click="loadDetail(articles[i].ident_art)"
            >
              <img :src="mediaBase + articles[i].image_art" class="img-sous-carte" :alt="articles[i].title_art">
              <div class="contenu ms-3 flex-grow-1"><h5>{{ articles[i].title_art }}</h5></div>
              <BoutonFav :articleId="articles[i].ident_art" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <ArticlePreviewPopup :payload="hoveredDetail" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BoutonFav from './boutonFav.vue'
import ArticlePreviewPopup from './ArticlePreviewPopup.vue'
import { fetchArticleDetail } from './detail.js'
import { recordArticleClick } from '../article-ui.js'

const router = useRouter()

defineProps({
  articles: { type: Array, required: true },
})

const hoveredDetail = ref(null)
const mediaBase = '/media/'

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
.card-principale { border: 1px solid #333; border-radius: 10px; padding: 20px; background-color: #f8f9fa; }
.sous-cartes { display: flex; flex-direction: column; gap: 10px; }
.carte-interne { border: 1px solid #aaa; border-radius: 8px; padding: 10px; display: flex; align-items: center; cursor: pointer; transition: transform 0.2s; }
.carte-interne:hover { transform: scale(1.02); }
.img-sous-carte { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
.contenu { margin-left: 10px; flex-grow: 1; }
</style>
