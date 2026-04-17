<template>
  <aside v-if="payload && payload.detail" class="preview-popup">
    <div class="preview-header">
      <p class="preview-label">Aperçu de l'article</p>
      <h3>{{ payload.detail.title_art || payload.article?.title_art || 'Article' }}</h3>
    </div>

    <div class="preview-body">
      <p v-if="payload.detail.reporter_name">
        <strong>Auteur :</strong> {{ payload.detail.reporter_name }}
      </p>
      <p v-if="payload.detail.readtime_art">
        <strong>Temps de lecture :</strong> {{ payload.detail.readtime_art }} min
      </p>
      <p v-if="payload.detail.category_name">
        <strong>Catégorie :</strong> {{ payload.detail.category_name }}
      </p>
      <p v-if="payload.detail.date_art">
        <strong>Date :</strong> {{ payload.detail.date_art }}
      </p>
      <p v-if="payload.detail.word_count">
        <strong>Mots :</strong> {{ payload.detail.word_count }}
      </p>
      <p v-if="payload.detail.hook_art" class="preview-hook">
        {{ payload.detail.hook_art }}
      </p>
    </div>
  </aside>
</template>

<script setup>
defineProps({
  payload: {
    type: Object,
    default: null,
  },
})
</script>

<style scoped>
.preview-popup {
  position: fixed;
  top: 100px;
  right: 30px;
  width: min(320px, calc(100vw - 2rem));
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.16);
  z-index: 100;
  overflow: hidden;
}

.preview-header {
  padding: 1rem 1.25rem;
  background: linear-gradient(135deg, #1d4ed8 0%, #0f766e 100%);
  color: white;
}

.preview-label {
  margin: 0 0 0.35rem 0;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  opacity: 0.85;
}

.preview-header h3 {
  margin: 0;
  font-size: 1.05rem;
  line-height: 1.4;
}

.preview-body {
  padding: 1rem 1.25rem 1.25rem;
}

.preview-body p {
  margin: 0 0 0.75rem 0;
  color: #4b5563;
  font-size: 0.92rem;
}

.preview-body strong {
  color: #111827;
}

.preview-hook {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  font-style: italic;
}

@media (max-width: 768px) {
  .preview-popup {
    display: none;
  }
}
</style>
