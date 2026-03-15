<template>
  <div class="container py-4">
    <h1 class="mb-4">À propos du projet Presse</h1>
    <p class="text-muted">Date du Livrable 1 : FrontEnd 05/11/2025</p>

    <!--Accueil-->
    <section class="doc-block">
      <h3>Accueil</h3>
      <p><strong>Vue :</strong> <code>src/page/Accueil.vue</code></p>
      <p><strong>Fonction :</strong> Page d'accueil affichant la Une, les articles principaux ainsi que secondaires. Ce dernier voit ses colonnes disposées en fonction du choix de l'utilisateur.</p>
    </section>

    <!-- Section: Article -->
    <section class="doc-block">
      <h3>Article (Affichage détaillé individuel)</h3>
      <p><strong>Données :</strong> fournies par <code>src/data/articles.js</code>.</p>
      <p><strong>Fonction :</strong> Récupérer les données de <code>Articles.js</code>, et les afficher l'un en dessous de l'autre.</p>
    </section>

    <!-- Section: Navbar visuelle -->
    <section class="doc-block">
      <h3>Navbar</h3>
      <p><strong>Composant :</strong> <code>src/components/Navbar.vue</code></p>
      <p><strong>Fonction :</strong> Sélection de police, couleur de texte, mode d'affichage (images / texte / défaut). Les sauvegarde couleur dans <code>localStorage</code>. Permet aussi le changement de page grace au router.</p>
      <p><strong>Compteur :</strong> clics globaux enregistrés dans <code>localStorage</code> (clé <code>clickCount</code>).</p>
    </section>

    <!-- Section: Favoris -->
    <section class="doc-block">
      <h3>Favoris</h3>
      <p><strong>Vue :</strong> <code>src/page/Favoris.vue</code></p>
      <p><strong>Composant JS :</strong> <code>src/components/boutonFav.vue</code></p>
      <p><strong>Fonction :</strong> Ajouter/Retirer les articles cliqués grâce au component <code>boutonFav.vue</code>.</p>
      <p><strong>Structure des données :</strong> chaque favori contient <code>{title, text, img }</code>.</p>
    </section>

    <!-- Section: Recherche (Formulaire) -->
    <section class="doc-block">
      <h3>Recherche</h3>
      <p><strong>Vue :</strong> <code>src/page/Formulaire.vue</code></p>
      <p><strong>Logique :</strong> filtres réactifs (mot-clé titre, catégorie, auteur) appliqués localement via <code>computed()</code>.</p>
      <p><strong>Mécanisme :</strong> <code>filteredArticles</code> retourne un sous-ensemble basé sur les conditions cumulées.</p>
    </section>

    
    <!-- Section: Connexion -->
    <section class="doc-block">
      <h3>Connexion</h3>
      <p><strong>Vue :</strong> <code>src/page/Connexion.vue</code></p>
      <p><strong>Fonction :</strong> Formulaire simulé (email, mot de passe").</p>
    </section>

    <!-- Section: Footer -->
    <section class="doc-block">
      <h3>Footer</h3>
      <p><strong>Composant :</strong> <code>src/page/Footer.vue</code></p>
      <p><strong>Fonction :</strong> zone basse du site avec liens rapides et branding. Style collé visuellement au bas de page.</p>
    </section>

  </div>
</template>


<style scoped>
.doc-block { border:1px solid #ddd; padding:16px; border-radius:8px; margin-bottom:24px; background:#fafafa; }
.doc-block h3 { margin-top:0; font-size:1.15rem; }
code { background:#eee; padding:2px 5px; border-radius:4px; font-size:0.85rem; }
ul { margin:0; padding-left:20px; }
</style>

