<?php
/**
 * detail_fetch.php - Controller pour le detail d'un article
 * Livrable 3 - Bloc A : Affichage des details d'un article
 * S'appuie sur les fonctions du MODEL (ArticleModel, CategoryModel, ReporterModel)
 */

function controller_detail_fetch(): array
{
    $id = (int)($_REQUEST['id'] ?? 0);
    if ($id <= 0) {
        return ['success' => false, 'error' => 'ID invalide'];
    }

    $article = ArticleModel::getById($id);
    if (empty($article)) {
        return ['success' => false, 'error' => 'Article introuvable'];
    }

    // Role de l'utilisateur connecte (visitor par defaut)
    $role = $_SESSION['user']['role'] ?? 'visitor';

    // Compteur de survols en session
    $_SESSION['article_hover_total'] = (int)($_SESSION['article_hover_total'] ?? 0) + 1;

    // Informations de base (tous les roles : visitor, user, admin)
    $detail = [
        'ident_art'    => $article['ident_art'] ?? null,
        'date_art'     => $article['date_art'] ?? null,
        'readtime_art' => $article['readtime_art'] ?? null,
        'hook_art'     => $article['hook_art'] ?? null,
    ];

    // Nombre de mots (champ calcule)
    $content = strip_tags($article['content_art'] ?? '');
    $detail['word_count'] = str_word_count($content);

    // user et admin : + categorie
    if ($role === 'user' || $role === 'admin') {
        $catId = (int)($article['fk_category_art'] ?? 0);
        $catName = 'Non categorise';
        if ($catId > 0) {
            $categories = CategoryModel::getAll();
            foreach ($categories as $cat) {
                $cid = (int)($cat['ident_cat'] ?? $cat['id_cat'] ?? 0);
                if ($cid === $catId) {
                    $catName = $cat['name_cat'] ?? $cat['label_cat'] ?? 'Inconnue';
                    break;
                }
            }
        }
        $detail['category_name'] = $catName;
    }

    // admin : + titre, auteur, id, id de l'image
    if ($role === 'admin') {
        $detail['title_art'] = $article['title_art'] ?? null;
        $detail['image_art'] = $article['image_art'] ?? null;

        // Récupérer le nom du reporter (t_reporter.name_rep)
        $repId = (int)($article['reporter_art'] ?? 0);
        if ($repId > 0) {
            $reporter = ReporterModel::getById($repId);
            // Colonne unique: name_rep (pas firstname_rep/lastname_rep)
            $detail['reporter_name'] = !empty($reporter)
                ? ($reporter['name_rep'] ?? 'Inconnu')
                : 'Inconnu';
        } else {
            $detail['reporter_name'] = 'Inconnu';
        }
    }

    return [
        'success' => true,
        'article' => $article,
        'detail'  => $detail,
        'role'    => $role,
    ];
}
