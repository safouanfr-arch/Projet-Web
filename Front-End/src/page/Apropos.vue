<template>
  <div class="container py-4">
    <h1 class="mb-4">À propos du projet Presse</h1>
    <p class="text-muted intro">
      Cette version correspond au projet final intégré : interface Vue.js, back-end PHP MVC et API JSON branchée sur le routeur AWebWiz.
    </p>

    <section class="doc-block">
      <h3>Architecture générale</h3>
      <p><strong>Front-end :</strong> application Vue.js avec <code>vue-router</code> et composants réutilisables.</p>
      <p><strong>Back-end :</strong> application PHP organisée en <code>model</code>, <code>view</code> et <code>controller</code>.</p>
      <p><strong>API :</strong> appels asynchrones via <code>index.php?page=api_*</code>, consommés depuis <code>src/api.js</code>.</p>
    </section>

    <section class="doc-block">
      <h3>Accueil</h3>
      <p><strong>Vue :</strong> <code>src/page/Accueil.vue</code></p>
      <p><strong>Fonction :</strong> affiche la Une, les articles principaux et les grilles secondaires en 2, 3 ou 4 colonnes.</p>
      <p><strong>Option utilisateur :</strong> un bouton du header permet de basculer les articles principaux en mode titres seuls.</p>
    </section>

    <section class="doc-block">
      <h3>Détail d'article</h3>
      <p><strong>Vue :</strong> <code>src/page/ArticleDetail.vue</code></p>
      <p><strong>Données :</strong> récupérées via l'API <code>api_detail</code> et sécurisées côté serveur avant affichage.</p>
      <p><strong>Survol :</strong> les cartes utilisent un aperçu latéral commun avec titre, auteur, catégorie et temps de lecture.</p>
    </section>

    <section class="doc-block">
      <h3>Navbar</h3>
      <p><strong>Composant :</strong> <code>src/components/Navbar.vue</code></p>
      <p><strong>Fonction :</strong> navigation, session utilisateur, bannière distante, choix de police et de couleur.</p>
      <p><strong>Statistiques :</strong> le header affiche le nombre de clics sur les liens d'articles, persisté dans <code>localStorage</code>.</p>
    </section>

    <section class="doc-block">
      <h3>Favoris</h3>
      <p><strong>Vue :</strong> <code>src/page/Favoris.vue</code></p>
      <p><strong>Composant :</strong> <code>src/components/boutonFav.vue</code></p>
      <p><strong>Fonction :</strong> ajout et retrait des favoris via l'API et la session PHP, puis affichage sur une page dédiée.</p>
    </section>

    <section class="doc-block">
      <h3>Recherche</h3>
      <p><strong>Vue :</strong> <code>src/page/Formulaire.vue</code></p>
      <p><strong>Filtres :</strong> mot-clé dans le titre, catégorie et auteur.</p>
      <p><strong>Traitement :</strong> la recherche est effectuée côté back-end puis restituée dynamiquement dans l'interface.</p>
    </section>

    <section class="doc-block">
      <h3>Footer</h3>
      <p><strong>Composant :</strong> <code>src/page/Footer.vue</code></p>
      <p><strong>Fonction :</strong> liens rapides, branding et vérification du nombre d'articles publiés pour une date donnée via l'API.</p>
    </section>
  </div>
</template>

<style scoped>
.intro {
  max-width: 900px;
}

.doc-block {
  border: 1px solid #ddd;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
  background: #fafafa;
}

.doc-block h3 {
  margin-top: 0;
  font-size: 1.15rem;
}

code {
  background: #eee;
  padding: 2px 5px;
  border-radius: 4px;
  font-size: 0.85rem;
}
</style>
