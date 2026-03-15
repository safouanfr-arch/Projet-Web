<?php

function main_favorite(): string
{
    // actions (session favorites)
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['fav_action'] ?? '') === 'remove') {
        $id = (int)($_POST['ident_art'] ?? 0);
        $favorites = $_SESSION['favorites'] ?? [];
        $favorites = is_array($favorites) ? $favorites : [];
        $favorites = array_map('intval', $favorites);

        if ($id > 0) {
            $favorites = array_values(array_diff($favorites, [$id]));
        }

        $_SESSION['favorites'] = $favorites;
        header('Location: ?page=favorite'); // recharge la page pour éviter le repost du formulaire
        exit;
    }

    // model
    $favorites = $_SESSION['favorites'] ?? [];
    $favorites = is_array($favorites) ? $favorites : [];
    $favorites = array_map('intval', $favorites);

    $articles = ArticleModel::getByIds($favorites);

    // view
    return join("\n", [
        html_head(get_menu()),
        html_favorites_page($articles),
        html_foot(),
    ]);
}

