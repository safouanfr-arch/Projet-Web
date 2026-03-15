<?php

function main_home():string
{
    if (($_POST['home_action'] ?? '') === 'toggle_main_compact') {
        $current = (string)($_COOKIE['home_main_compact'] ?? '') === '1'; // true si actuellement compact, false sinon
        $newValue = $current ? '0' : '1';
        $expire = time() + (60 * 60 * 24 * 30);
        setcookie('home_main_compact', $newValue, $expire, '/');
        header('Location: ?page=home');
        exit;
    }

    if (($_POST['home_action'] ?? '') === 'set_secondary_cols') {
        $cols = (int)($_POST['home_secondary_cols'] ?? 3);
        $expire = time() + (60 * 60 * 24 * 30);
        setcookie('home_secondary_cols', (string)$cols, $expire, '/');
        header('Location: ?page=home');
        exit;
    }

    // model
    $all_articles = ArticleModel::getLatest(10);
    
    // organiser les articles
    $featured = isset($all_articles[0]) ? $all_articles[0] : null;
    $main = array_slice($all_articles, 1, 3);
    $secondary = array_slice($all_articles, 4, 6);

    $excludeIds = [];
    if (!empty($featured['ident_art'])) {
        $excludeIds[] = (int)$featured['ident_art'];
    }
    foreach ($main as $art) {
        if (!empty($art['ident_art'])) {
            $excludeIds[] = (int)$art['ident_art'];
        }
    }
    foreach ($secondary as $art) {
        if (!empty($art['ident_art'])) {
            $excludeIds[] = (int)$art['ident_art'];
        }
    }

    $readtime3 = ArticleModel::getByReadtime(3, 10, $excludeIds);

    // view
	return join( "\n", [
		html_head(get_menu()),
        html_home_page($featured, $main, $secondary, $readtime3),
		html_foot(),
	]);

}