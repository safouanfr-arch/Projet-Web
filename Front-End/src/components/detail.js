/**
 * detail.js - Cache local pour le chargement asynchrone des details d'un article
 */

import { getArticleDetail } from '../api.js'

const detailCache = new Map()

export async function fetchArticleDetail(id) {
  const key = String(id)

  if (detailCache.has(key)) {
    return detailCache.get(key)
  }

  const data = await getArticleDetail(id)
  if (data.success) {
    detailCache.set(key, data)
  }

  return data
}

export function clearDetailCache() {
  detailCache.clear()
}
