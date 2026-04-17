<template>
  <div class="container mt-4">
    <div class="row" v-if="articles.length >= 10">
      <div class="col-md-6">
        <div class="card-principale p-3">
          <div class="sous-cartes mt-3">
            <div
              v-for="art in articles.slice(0, 5)"
              :key="art.ident_art"
              class="carte-interne d-flex align-items-center mb-2 p-2"
              @mouseover="loadHoverDetail(art.ident_art)"
              @mouseleave="hoveredDetail = null"
              @click="loadDetail(art.ident_art)"
            >
              <img :src="mediaBase + art.image_art" :alt="art.title_art" class="image">
              <div class="contenu ms-3 flex-grow-1">
                <h5>{{ art.title_art }}</h5>
              </div>
              <BoutonFav :articleId="art.ident_art" />
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card-principale p-3">
          <div class="sous-cartes mt-3">
            <div
              v-for="art in articles.slice(5, 10)"
              :key="art.ident_art"
              class="carte-interne d-flex align-items-center mb-2 p-2"
              @mouseover="loadHoverDetail(art.ident_art)"
              @mouseleave="hoveredDetail = null"
              @click="loadDetail(art.ident_art)"
            >
              <img :src="mediaBase + art.image_art" :alt="art.title_art" class="image">
              <div class="contenu ms-3 flex-grow-1">
                <h5>{{ art.title_art }}</h5>
              </div>
              <BoutonFav :articleId="art.ident_art" />
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
.container { margin: 80px auto; }
.card-principale { border: 1px solid #333; border-radius: 10px; padding: 20px; background-color: #f8f9fa; }
.sous-cartes { display: flex; flex-direction: column; gap: 10px; }
.carte-interne { border: 1px solid #aaa; border-radius: 8px; background-color: #fff; padding: 10px; display: flex; align-items: center; transition: transform 0.2s; cursor: pointer; }
.carte-interne:hover { transform: scale(1.02); }
.image { width: 75px; height: 60px; object-fit: cover; border-radius: 5px; }
.contenu { margin-left: 10px; flex-grow: 1; }
</style>
