<?php

function main_api_articles(): string
{
    $limit = isset($_REQUEST['limit']) ? (int)$_REQUEST['limit'] : null;
    $offset = (int)($_REQUEST['offset'] ?? 0);
    $articles = ArticleModel::getAll($limit, $offset);

    return api_json_response([
        'success' => true,
        'articles' => $articles,
        'count' => count($articles),
    ]);
}

function main_api_articles_by_date(): string
{
    $date = trim((string)($_REQUEST['date'] ?? ''));
    if ($date === '') {
        return api_json_response(['success' => false, 'error' => 'Date requise'], 400);
    }

    return api_json_response([
        'success' => true,
        'count' => ArticleModel::countByDate($date),
        'date' => $date,
    ]);
}
