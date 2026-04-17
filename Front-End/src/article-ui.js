import { ref } from 'vue'

const ARTICLE_CLICK_STORAGE_KEY = 'articleLinkClicks'
const MAIN_ARTICLES_COMPACT_STORAGE_KEY = 'mainArticlesCompact'

function readStoredNumber(key, fallback = 0) {
  const raw = localStorage.getItem(key)
  const value = Number.parseInt(raw || '', 10)
  return Number.isFinite(value) && value >= 0 ? value : fallback
}

function readStoredBoolean(key) {
  return localStorage.getItem(key) === '1'
}

export const articleLinkClickCount = ref(readStoredNumber(ARTICLE_CLICK_STORAGE_KEY, 0))
export const mainArticlesCompact = ref(readStoredBoolean(MAIN_ARTICLES_COMPACT_STORAGE_KEY))

export function recordArticleClick() {
  articleLinkClickCount.value += 1
  localStorage.setItem(ARTICLE_CLICK_STORAGE_KEY, String(articleLinkClickCount.value))
}

export function resetArticleClickCount() {
  articleLinkClickCount.value = 0
  localStorage.setItem(ARTICLE_CLICK_STORAGE_KEY, '0')
}

export function setMainArticlesCompact(value) {
  mainArticlesCompact.value = Boolean(value)
  localStorage.setItem(MAIN_ARTICLES_COMPACT_STORAGE_KEY, mainArticlesCompact.value ? '1' : '0')
}

export function toggleMainArticlesCompact() {
  setMainArticlesCompact(!mainArticlesCompact.value)
}
