<?php

function main_search():string
{
    // model
    $categories = CategoryModel::getAll();
    $reporters = ReporterModel::getAll();

    $criteria = [
        'keyword' => '',
        'category_id' => 0,
        'reporter_id' => 0,
    ];

    // récupère les inputs (hyper simple : POST)
    if (!empty($_POST['keyword'])) {
        $criteria['keyword'] = trim((string)$_POST['keyword']);
    }
    if (!empty($_POST['category_id'])) {
        $criteria['category_id'] = (int)$_POST['category_id'];
    }
    if (!empty($_POST['reporter_id'])) {
        $criteria['reporter_id'] = (int)$_POST['reporter_id'];
    }
    // Si aucun critère : on affiche les plus récents (comportement demandé)
    $hasCriteria = (
        $criteria['keyword'] !== '' ||
        $criteria['category_id'] > 0 ||
        $criteria['reporter_id'] > 0
    );

    $results = $hasCriteria ? ArticleModel::search($criteria) : ArticleModel::getLatest(10);

    // view
	return join( "\n", [
		html_head(get_menu()),
        html_search_page($criteria, $categories, $reporters, $results),
		html_foot(),
	]);

}

