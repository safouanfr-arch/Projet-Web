<?php
/**
 * detail_fetch.php - Controller pour le detail d'un article
 * Livrable 3 - Bloc A : Affichage des details d'un article
 * S'appuie sur les fonctions du MODEL (ArticleModel, CategoryModel, ReporterModel)
 */

function main_api_detail(): string
{
    return api_json_response(controller_detail_fetch());
}

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

    $role = $_SESSION['user']['role'] ?? 'visitor';
    $_SESSION['article_hover_total'] = (int)($_SESSION['article_hover_total'] ?? 0) + 1;

    $article['content_art'] = sanitize_article_html((string)($article['content_art'] ?? ''));

    $catId = (int)($article['fk_category_art'] ?? 0);
    $categoryName = 'Non categorise';
    if ($catId > 0) {
        $categories = CategoryModel::getAll();
        foreach ($categories as $cat) {
            $candidateId = (int)($cat['ident_cat'] ?? $cat['id_cat'] ?? 0);
            if ($candidateId === $catId) {
                $categoryName = $cat['name_cat'] ?? $cat['label_cat'] ?? 'Inconnue';
                break;
            }
        }
    }

    $reporterId = (int)($article['reporter_art'] ?? 0);
    $reporterName = 'Inconnu';
    if ($reporterId > 0) {
        $reporter = ReporterModel::getById($reporterId);
        if (!empty($reporter)) {
            $reporterName = $reporter['name_rep'] ?? 'Inconnu';
        }
    }

    $detail = [
        'ident_art' => $article['ident_art'] ?? null,
        'title_art' => $article['title_art'] ?? null,
        'image_art' => $article['image_art'] ?? null,
        'date_art' => $article['date_art'] ?? null,
        'readtime_art' => $article['readtime_art'] ?? null,
        'hook_art' => $article['hook_art'] ?? null,
        'category_name' => $categoryName,
        'reporter_name' => $reporterName,
    ];

    $content = strip_tags((string)($article['content_art'] ?? ''));
    $detail['word_count'] = str_word_count($content);

    return [
        'success' => true,
        'article' => $article,
        'detail' => $detail,
        'role' => $role,
    ];
}
