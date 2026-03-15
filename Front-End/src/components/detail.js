/**
 * detail.js - Module pour le chargement asynchrone des details d'un article
 * Livrable 3 - Bloc A : Affichage des details via fetch()
 * Fichier separe comme requis par les specifications
 *
 * Architecture :
 *   1. Le navigateur declenche fetchArticleDetail(id) lors du survol souris
 *   2. fetch() envoie une requete asynchrone vers le controller PHP detail_fetch.php
 *   3. Le controller interroge la BDD via le MODEL (ArticleModel)
 *   4. Les details retournes sont affiches dans un <div> sur la page
 *   5. Les details varient en fonction du role (visitor, user, admin)
 */

// Cache pour eviter les requetes repetees sur le meme article
const detailCache = new Map()

/**
 * Recupere les details d'un article depuis la base de donnees via fetch()
 * Appelle le controller detail_fetch.php cote serveur
 *
 * @param {number|string} id - L'identifiant de l'article
 * @returns {Promise<Object>} - Les details de l'article avec le role
 */
export async function fetchArticleDetail(id) {
  const key = String(id)

  // Retourner depuis le cache si deja charge
  if (detailCache.has(key)) {
    return detailCache.get(key)
  }

  // Requete asynchrone via fetch() vers le controller PHP
  const response = await fetch(`/api.php?action=detail&id=${encodeURIComponent(id)}`)
  if (!response.ok) {
    throw new Error(`Erreur HTTP ${response.status}`)
  }

  const data = await response.json()

  // Mettre en cache si succes
  if (data.success) {
    detailCache.set(key, data)
  }

  return data
}

/**
 * Vide le cache des details (utile apres un changement de role/connexion)
 */
export function clearDetailCache() {
  detailCache.clear()
}
