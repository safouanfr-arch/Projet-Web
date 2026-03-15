<?php

function main_articles(): string
{
    // actions (session favorites)
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['fav_action'] ?? '') === 'add') {
        $id = (int)($_POST['ident_art'] ?? 0);
        if ($id > 0) {
            $favorites = $_SESSION['favorites'] ?? [];

            if (!in_array($id, $favorites)) {
                $favorites[] = $id;
            }

            $_SESSION['favorites'] = $favorites;
        }

        header('Location: ?page=articles');
        exit;
    }

    // model
    $articles = ArticleModel::getAll();
    $favorites = $_SESSION['favorites'] ?? [];
    $favorites = is_array($favorites) ? $favorites : [];
    $favorites = array_map('intval', $favorites);

    // view
    return join("\n", [
        html_head(get_menu()),
        html_articles_list($articles, $favorites),
        html_foot(),
    ]);
}

