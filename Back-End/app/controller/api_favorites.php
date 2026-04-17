<?php

function main_api_favorite_add(): string
{
    $id = (int)($_REQUEST['id'] ?? 0);
    if ($id <= 0) {
        return api_json_response(['success' => false, 'error' => 'ID invalide'], 400);
    }

    $favorites = api_get_favorite_ids();
    if (!in_array($id, $favorites, true)) {
        $favorites[] = $id;
    }

    api_save_favorite_ids($favorites);
    return api_json_response(api_build_favorite_response($id));
}

function main_api_favorite_remove(): string
{
    $id = (int)($_REQUEST['id'] ?? 0);
    if ($id <= 0) {
        return api_json_response(['success' => false, 'error' => 'ID invalide'], 400);
    }

    $favorites = array_values(array_diff(api_get_favorite_ids(), [$id]));
    api_save_favorite_ids($favorites);

    return api_json_response(api_build_favorite_response($id));
}

function main_api_favorite_clear(): string
{
    api_save_favorite_ids([]);

    return api_json_response([
        'success' => true,
        'favorites' => [],
        'count' => 0,
        'ids' => [],
    ]);
}

function main_api_favorite_list(): string
{
    $favorites = api_get_favorite_ids();
    $articles = empty($favorites) ? [] : ArticleModel::getByIds($favorites);

    return api_json_response([
        'success' => true,
        'articles' => $articles,
        'ids' => $favorites,
        'count' => count($favorites),
    ]);
}

function main_api_favorite_check(): string
{
    $id = (int)($_REQUEST['id'] ?? 0);
    $favorites = api_get_favorite_ids();

    return api_json_response([
        'success' => true,
        'is_favorite' => in_array($id, $favorites, true),
    ]);
}
