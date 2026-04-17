<template>
  <button class="btn btn-light border-0 btn-favori" @click.stop="toggleFavori">
    <i :class="heartClass"></i>
  </button>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { addFavorite, removeFavorite, checkFavorite } from '../api.js'

const props = defineProps({
  articleId: {
    type: [Number, String],
    default: null
  }
})

const isFavori = ref(false)

const heartClass = computed(() =>
  isFavori.value ? 'fas fa-heart text-danger' : 'far fa-heart text-dark'
)

async function toggleFavori() {
  if (!props.articleId) return
  try {
    if (isFavori.value) {
      const data = await removeFavorite(props.articleId)
      if (data.success) {
        isFavori.value = false
        window.dispatchEvent(new CustomEvent('session-changed'))
      }
    } else {
      const data = await addFavorite(props.articleId)
      if (data.success) {
        isFavori.value = true
        window.dispatchEvent(new CustomEvent('session-changed'))
      }
    }
  } catch (e) {
    console.error('Erreur toggle favori:', e)
  }
}

async function checkState() {
  if (!props.articleId) return
  try {
    const data = await checkFavorite(props.articleId)
    if (data.success) {
      isFavori.value = data.is_favorite
    }
  } catch (e) {
    console.error('Erreur check favori:', e)
  }
}

onMounted(checkState)
watch(() => props.articleId, checkState)
</script>

<style scoped>
.btn-favori {
  font-size: 24px;
  cursor: pointer;
  background: transparent;
  flex-shrink: 0;
}
</style>
