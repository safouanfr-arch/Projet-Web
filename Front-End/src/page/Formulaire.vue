<template>
  <div id="principal-container" class="formulaire-wrapper">
    <div class="container mt-4 mb-5">
      <div class="formulaire-header mb-5">
        <h1>Recherche d'articles</h1>
        <p class="subtitle">Trouvez les articles qui vous intéressent</p>
      </div>

      <div class="row g-4">
        <!-- COLONNE GAUCHE : FORMULAIRE DE RECHERCHE -->
        <aside class="col-lg-4">
          <div class="search-card">
            <h4 class="search-title">Filtres de recherche</h4>

            <form @submit.prevent="lancerRecherche" class="search-form">
              <!-- Mot-clé -->
              <div class="form-group">
                <label for="keyword" class="form-label">Mot-clé</label>
                <input
                  id="keyword"
                  type="text"
                  v-model="keyword"
                  placeholder="Entrez votre recherche..."
                  class="form-control"
                />
              </div>

              <!-- Catégorie -->
              <div class="form-group">
                <label for="category" class="form-label">Catégorie</label>
                <select id="category" v-model="selectedCategory" class="form-select">
                  <option :value="0">Toutes les catégories</option>
                  <option v-for="cat in categories" :key="cat.ident_cat || cat.id_cat" :value="cat.ident_cat || cat.id_cat">
                    {{ cat.name_cat || cat.label_cat }}
                  </option>
                </select>
              </div>

              <!-- Auteur / Reporter -->
              <div class="form-group">
                <label for="reporter" class="form-label">Auteur</label>
                <select id="reporter" v-model="selectedReporter" class="form-select">
                  <option :value="0">Tous les auteurs</option>
                  <option v-for="rep in reporters" :key="rep.id_rep" :value="rep.id_rep">
                    {{ rep.firstname_rep || '' }} {{ rep.lastname_rep || rep.name_rep || rep.label_rep || '' }}
                  </option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary w-100 search-btn">
                <span v-if="!loading">🔍 Rechercher</span>
                <span v-else><span class="spinner-mini"></span> Recherche...</span>
              </button>
            </form>
          </div>
        </aside>

        <!-- COLONNE DROITE : RÉSULTATS -->
        <main class="col-lg-8">
          <div class="results-header mb-4">
            <h4 class="results-title">Résultats</h4>
            <p class="results-count" v-if="!loading">{{ results.length }} article(s) trouvé(s)</p>
          </div>

          <div v-if="loading" class="loading-state">
            <div class="spinner-large"></div>
            <p>Recherche en cours...</p>
          </div>

          <section v-else class="results-grid">
            <template v-if="results.length > 0">
              <article
                v-for="art in results"
                :key="art.ident_art"
                class="result-card"
                @click="loadDetail(art.ident_art)"
              >
                <div class="result-card-inner">
                  <div class="result-meta">
                    <span class="result-date">{{ art.date_art }}</span>
                    <span class="result-time">{{ art.readtime_art }} min</span>
                  </div>
                  <h3 class="result-title">{{ art.title_art }}</h3>
                  <p class="result-hook">{{ art.hook_art }}</p>
                  <div class="result-footer">
                    <span class="result-link">Lire →</span>
                  </div>
                </div>
              </article>
            </template>
            <div v-else class="no-results">
              <p class="no-results-icon">📭</p>
              <p class="no-results-text">Aucun article ne correspond à votre recherche.</p>
            </div>
          </section>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { searchArticles } from '../api.js'
import { recordArticleClick } from '../article-ui.js'

const router = useRouter()
const keyword = ref('')
const selectedCategory = ref(0)
const selectedReporter = ref(0)
const categories = ref([])
const reporters = ref([])
const results = ref([])
const loading = ref(false)

onMounted(() => {
  // Charger les catégories, reporters et les articles récents au montage
  lancerRecherche()
})

function lancerRecherche() {
  loading.value = true
  searchArticles(keyword.value, selectedCategory.value, selectedReporter.value)
    .then((data) => {
      if (data.success) {
        results.value = data.results
        if (data.categories) categories.value = data.categories
        if (data.reporters) reporters.value = data.reporters
      }
    })
    .catch((e) => {
      console.error('Erreur recherche:', e)
    })
    .finally(() => {
      loading.value = false
    })
}

function loadDetail(id) {
  recordArticleClick()
  router.push({ name: 'ArticleDetail', params: { id } })
}
</script>

<style scoped>
.formulaire-wrapper {
  background: #f9fafb;
  padding: 2rem 0;
  min-height: calc(100vh - 200px);
}

.formulaire-header {
  text-align: center;
  padding-bottom: 2rem;
  border-bottom: 2px solid #e5e7eb;
}

.formulaire-header h1 {
  color: #1f2937;
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #6b7280;
  font-size: 1.05rem;
}

/* Formulaire de recherche */
.search-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid #e5e7eb;
  position: sticky;
  top: 100px;
}

.search-title {
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.search-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  color: #374151;
  font-weight: 600;
  font-size: 0.95rem;
}

.form-control,
.form-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  background: #f9fafb;
  font-family: inherit;
}

.form-control:focus,
.form-select:focus {
  outline: none;
  border-color: var(--primary-color);
  background: white;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-btn {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.search-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
}

.spinner-mini {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Résultats */
.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.results-title {
  color: #1f2937;
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0;
}

.results-count {
  color: #6b7280;
  font-size: 0.95rem;
  margin: 0;
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

.results-grid {
  display: grid;
  gap: 1.5rem;
}

.result-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.result-card:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
  border-color: var(--primary-color);
}

.result-card-inner {
  padding: 1.5rem;
}

.result-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.85rem;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
}

.result-date {
  color: #6b7280;
  font-weight: 500;
  background: #f3f4f6;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
}

.result-time {
  color: #6b7280;
  font-weight: 500;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
}

.result-title {
  color: #1f2937;
  font-size: 1.2rem;
  font-weight: 700;
  margin: 0.75rem 0;
  line-height: 1.3;
}

.result-card:hover .result-title {
  color: var(--primary-color);
}

.result-hook {
  color: #4b5563;
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.result-footer {
  display: flex;
  justify-content: flex-end;
}

.result-link {
  color: var(--primary-color);
  font-weight: 600;
  transition: all 0.3s ease;
}

.result-card:hover .result-link {
  transform: translateX(4px);
  color: var(--secondary-color);
}

.no-results {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  border: 2px dashed #e5e7eb;
}

.no-results-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.no-results-text {
  color: #6b7280;
  font-size: 1rem;
  margin: 0;
}

/* Responsive */
@media (max-width: 991px) {
  .search-card {
    position: relative;
    top: auto;
  }

  .formulaire-header h1 {
    font-size: 2rem;
  }
}
</style>
