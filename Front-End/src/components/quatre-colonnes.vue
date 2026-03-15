<template>
  <div class="container mt-4">
    <div class="row g-4" v-if="articles.length >= 8">
      <!-- 4 cartes principales avec 2 articles chacune -->
      <div class="col-md-3" v-for="(col, ci) in [[0,1],[2,3],[4,5],[6,7]]" :key="ci">
        <div class="card-principale p-3">
          <div class="sous-cartes mt-3">
            <div class="carte-interne"
                 v-for="i in col" :key="articles[i].ident_art"
                 @mouseover="loadHoverDetail(articles[i].ident_art)"
                 @mouseleave="hoveredDetail = null"
                 @click="loadDetail(articles[i].ident_art)">
              <img :src="mediaBase + articles[i].image_art" class="img-sous-carte" :alt="articles[i].title_art"/>
              <div class="contenu ms-3 flex-grow-1"><h5>{{ articles[i].title_art }}</h5></div>
              <BoutonFav :articleId="articles[i].ident_art" />
            </div>
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
import BoutonFav from './boutonFav.vue'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { fetchArticleDetail } from './detail.js'

const router = useRouter()

const props = defineProps({
  articles: { type: Array, required: true }
})

const hoveredDetail = ref(null)
const mediaBase = '/media/'

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
.card-principale { border: 1px solid #333; border-radius: 10px; padding: 20px; background-color: #f8f9fa; }
.sous-cartes { display: flex; flex-direction: column; gap: 10px; }
.carte-interne { border: 1px solid #aaa; border-radius: 8px; padding: 10px; display: flex; align-items: center; cursor: pointer; transition: transform 0.2s; }
.carte-interne:hover { transform: scale(1.02); }
.img-sous-carte { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
.contenu { margin-left: 10px; flex-grow: 1; }
.preview-popup { position: fixed; top: 100px; right: 30px; width: 250px; padding: 10px; background: #fff; border: 2px solid #333; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.2); z-index: 100; }
.detail-overlay { background-color: #fdfdfd; }
.detail-overlay img { max-height: 300px; object-fit: cover; width: 100%; }
</style>
