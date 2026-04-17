<?php

function main_api_search(): string
{
    $criteria = [
        'keyword' => trim((string)($_REQUEST['keyword'] ?? '')),
        'category_id' => (int)($_REQUEST['category_id'] ?? 0),
        'reporter_id' => (int)($_REQUEST['reporter_id'] ?? 0),
    ];

    $hasCriteria = (
        $criteria['keyword'] !== '' ||
        $criteria['category_id'] > 0 ||
        $criteria['reporter_id'] > 0
    );

    $results = $hasCriteria ? ArticleModel::search($criteria) : ArticleModel::getLatest(10);

    return api_json_response([
        'success' => true,
        'results' => $results,
        'count' => count($results),
        'categories' => CategoryModel::getAll(),
        'reporters' => ReporterModel::getAll(),
    ]);
}
