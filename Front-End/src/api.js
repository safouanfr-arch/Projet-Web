/**
 * api.js - Module centralise pour les appels fetch vers le back-end PHP
 * Toutes les requetes passent par le routeur MVC via index.php?page=api_*
 */

const API_BASE = '/index.php'

function apiCall(page, params = {}, method = 'GET') {
  const searchParams = new URLSearchParams({ page, ...(method === 'GET' ? params : {}) })
  const url = `${API_BASE}?${searchParams.toString()}`

  const options = {
    method,
    credentials: 'include',
  }

  if (method !== 'GET') {
    options.headers = { 'Content-Type': 'application/x-www-form-urlencoded' }
    options.body = new URLSearchParams(params).toString()
  }

  return fetch(url, options)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Erreur HTTP ${response.status}`)
      }

      const contentType = response.headers.get('content-type') || ''
      if (!contentType.includes('application/json')) {
        return response.text().then((body) => {
          throw new Error(`Reponse non JSON recue: ${body.slice(0, 120)}`)
        })
      }

      return response.json()
    })
}

export function getArticleDetail(id) {
  return apiCall('api_detail', { id })
}

export function getArticles(limit = null, offset = 0) {
  const params = { offset }
  if (limit) params.limit = limit
  return apiCall('api_articles', params)
}

export function getArticlesByDate(date) {
  return apiCall('api_articles_by_date', { date })
}

export function addFavorite(id) {
  return apiCall('api_favorite_add', { id }, 'POST')
}

export function removeFavorite(id) {
  return apiCall('api_favorite_remove', { id }, 'POST')
}

export function clearFavorites() {
  return apiCall('api_favorite_clear', {}, 'POST')
}

export function getFavorites() {
  return apiCall('api_favorite_list')
}

export function checkFavorite(id) {
  return apiCall('api_favorite_check', { id })
}

export function searchArticles(keyword = '', categoryId = 0, reporterId = 0) {
  return apiCall('api_search', {
    keyword,
    category_id: categoryId,
    reporter_id: reporterId,
  })
}

export function login(loginName, password) {
  return apiCall('api_login', { login: loginName, password }, 'POST')
}

export function logout() {
  return apiCall('api_logout', {}, 'POST')
}

export function getSession() {
  return apiCall('api_session')
}

export function getBanner() {
  return apiCall('api_banner')
}
