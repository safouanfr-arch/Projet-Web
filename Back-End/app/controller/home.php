<?php

function main_home():string
{
    controller_handle_global_layout_actions('home');

    if (($_POST['home_action'] ?? '') === 'toggle_main_compact') {
        $current = controller_get_home_preferences()['main_compact'];
        $newValue = $current ? '0' : '1';
        $expire = time() + (60 * 60 * 24 * 30);
        setcookie('home_main_compact', $newValue, $expire, '/');
        header('Location: ' . controller_page_url('home'));
        exit;
    }

    if (($_POST['home_action'] ?? '') === 'set_secondary_cols') {
        $cols = (int)($_POST['home_secondary_cols'] ?? 3);
        if (!in_array($cols, [2, 3, 4], true)) {
            $cols = 3;
        }
        $expire = time() + (60 * 60 * 24 * 30);
        setcookie('home_secondary_cols', (string)$cols, $expire, '/');
        header('Location: ' . controller_page_url('home'));
        exit;
    }

    // model
    $all_articles = ArticleModel::getLatest(10);
    $homePreferences = controller_get_home_preferences();
    
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
		html_head(get_menu(), controller_get_layout_context('home')),
        html_home_page(
            $featured,
            $main,
            $secondary,
            $readtime3,
            $homePreferences['main_compact'],
            $homePreferences['secondary_cols']
        ),
		html_foot(),
	]);

}
