<template>
  <div id="principal-container">
    <!-- Section principale avec articles en vedette -->
    <div class="hero-section">
      <principal :articles="articles" />
    </div>

  <!-- Section disposition des articles -->
  <div class="disposition-section">
    <div class="container">
      <!-- Titre et description -->
      <div class="section-header">
        <h2>Parcourez nos articles</h2>
        <p class="section-subtitle">Choisissez votre disposition préférée</p>
      </div>

      <!-- Choix du nombre de colonnes -->
      <div class="dispo-controls">
        <div class="dispo-label">Disposition :</div>

        <div class="radio-group">
          <label class="radio-option">
            <input type="radio" value="2" v-model="choixColonnes" class="radio-input">
            <span class="radio-text">2 colonnes</span>
          </label>

          <label class="radio-option">
            <input type="radio" value="3" v-model="choixColonnes" class="radio-input">
            <span class="radio-text">3 colonnes</span>
          </label>

          <label class="radio-option">
            <input type="radio" value="4" v-model="choixColonnes" class="radio-input">
            <span class="radio-text">4 colonnes</span>
          </label>
        </div>
      </div>

      <!-- Grille de cartes selon le choix -->
      <p class="text-muted" v-if="articles.length === 0">Chargement des articles...</p>

      <div class="cartes-grille" v-if="articles.length > 0">
        <deuxColonnes v-if="choixColonnes === '2'" :articles="articles" />
        <TroisColonnes v-else-if="choixColonnes === '3'" :articles="articles" />
        <quatreColonnes v-else :articles="articles" />
      </div>
    </div>
  </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import principal from '../components/principal.vue'
import deuxColonnes from '../components/deux-colonnes.vue'
import TroisColonnes from '../components/trois-colonnes.vue'
import quatreColonnes from '../components/quatre-colonnes.vue'
import { getArticles } from '../api.js'

const choixColonnes = ref('3')
const articles = ref([])

onMounted(async () => {
  try {
    const data = await getArticles()
    if (data.success) {
      articles.value = data.articles
    }
  } catch (e) {
    console.error('Erreur chargement articles accueil:', e)
  }
})
</script>

<style scoped>
/* Hero Section */
.hero-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
  width: 100%;
}

/* Section disposition */
.disposition-section {
  background: #f9fafb;
  padding: 3rem 1rem;
  margin-top: 2rem;
}

.disposition-section .container {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
  padding-bottom: 2rem;
  border-bottom: 2px solid #e5e7eb;
}

.section-header h2 {
  color: #1f2937;
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.section-subtitle {
  color: #6b7280;
  font-size: 1.05rem;
}

/* Contrôles de disposition */
.dispo-controls {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  flex-wrap: wrap;
}

.dispo-label {
  color: #1f2937;
  font-weight: 600;
  font-size: 1rem;
}

.radio-group {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.radio-option {
  display: flex;
  align-items: center;
  cursor: pointer;
  gap: 0.5rem;
}

.radio-input {
  cursor: pointer;
  width: 18px;
  height: 18px;
  accent-color: var(--primary-color);
}

.radio-text {
  color: #4b5563;
  font-weight: 500;
  user-select: none;
  cursor: pointer;
  transition: color 0.3s ease;
}

.radio-option:hover .radio-text {
  color: var(--primary-color);
}

/* Grille des cartes */
.cartes-grille {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* Responsive */
@media (max-width: 768px) {
  .disposition-section {
    padding: 2rem 0;
  }

  .section-header {
    margin-bottom: 2rem;
  }

  .section-header h2 {
    font-size: 1.5rem;
  }

  .dispo-controls {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .radio-group {
    width: 100%;
  }

  .cartes-grille {
    padding: 1rem;
  }
}
</style>
