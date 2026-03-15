/**
 * api.js - Module centralisé pour les appels fetch vers le back-end PHP
 * Toutes les requêtes passent par api.php avec credentials pour la session
 */

// URL relative — Vite proxy redirige /api.php vers http://back-end/public/api.php
const API_BASE = '/api.php'

async function apiCall(action, params = {}, method = 'GET') {
  let url = `${API_BASE}?action=${action}`
  const options = {
    method,
  }

  if (method === 'GET') {
    const query = new URLSearchParams(params).toString()
    if (query) url += '&' + query
  } else {
    options.headers = { 'Content-Type': 'application/x-www-form-urlencoded' }
    options.body = new URLSearchParams(params).toString()
  }

  const response = await fetch(url, options)
  if (!response.ok) {
    throw new Error(`Erreur HTTP ${response.status}`)
  }
  return response.json()
}

// BLOC A — Détail article
export function getArticleDetail(id) {
  return apiCall('detail', { id })
}

// BLOC A — Liste articles
export function getArticles(limit = null, offset = 0) {
  const params = { offset }
  if (limit) params.limit = limit
  return apiCall('articles', params)
}

// BLOC B — Favoris
export function addFavorite(id) {
  return apiCall('favorite_add', { id }, 'POST')
}

export function removeFavorite(id) {
  return apiCall('favorite_remove', { id }, 'POST')
}

export function clearFavorites() {
  return apiCall('favorite_clear', {}, 'POST')
}

export function getFavorites() {
  return apiCall('favorite_list')
}

export function checkFavorite(id) {
  return apiCall('favorite_check', { id })
}

// BLOC C — Recherche
export function searchArticles(keyword = '', categoryId = 0, reporterId = 0) {
  return apiCall('search', {
    keyword,
    category_id: categoryId,
    reporter_id: reporterId,
  })
}

// BLOC D — Login / Session
export function login(loginName, password) {
  return apiCall('login', { login: loginName, password }, 'POST')
}

export function logout() {
  return apiCall('logout', {}, 'POST')
}

export function getSession() {
  return apiCall('session')
}

// BLOC E — Bannière
export function getBanner() {
  return apiCall('banner')
}
