<?php

function api_prepare_response_headers(): void
{
    $origin = (string)($_SERVER['HTTP_ORIGIN'] ?? '');
    $allowedOrigin = '';

    if ($origin !== '' && preg_match('/^http:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/', $origin)) {
        $allowedOrigin = $origin;
    }

    if ($allowedOrigin !== '') {
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Vary: Origin');
    }

    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=utf-8');
}

function api_json_response(array $payload, int $status = 200): string
{
    api_prepare_response_headers();
    http_response_code($status);
    return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function api_handle_preflight_if_needed(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
        api_prepare_response_headers();
        http_response_code(204);
        exit;
    }
}

function api_get_favorite_ids(): array
{
    $favorites = $_SESSION['favorites'] ?? [];
    if (!is_array($favorites)) {
        return [];
    }

    $favorites = array_values(array_unique(array_filter(array_map('intval', $favorites), fn($id) => $id > 0)));
    sort($favorites);
    return $favorites;
}

function api_save_favorite_ids(array $favorites): void
{
    $normalized = array_values(array_unique(array_filter(array_map('intval', $favorites), fn($id) => $id > 0)));
    sort($normalized);
    $_SESSION['favorites'] = $normalized;
}

function api_build_favorite_response(?int $checkedId = null): array
{
    $favorites = api_get_favorite_ids();
    $articles = empty($favorites) ? [] : ArticleModel::getByIds($favorites);
    $titles = array_slice(array_column($articles, 'title_art'), 0, 5);

    return [
        'success' => true,
        'favorites' => $titles,
        'count' => count($favorites),
        'is_favorite' => $checkedId !== null ? in_array($checkedId, $favorites, true) : null,
        'ids' => $favorites,
    ];
}

function api_remote_json_get(string $url): ?array
{
    $response = @file_get_contents($url);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

function api_remote_json_post_form(string $url, array $fields, int $timeout = 10): ?array
{
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($fields),
            'timeout' => $timeout,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

function api_normalize_banner_payload(array $banner): array
{
    $source = $banner['banner_4IPDW'] ?? $banner;

    return [
        'url' => $source['link'] ?? $source['url'] ?? '',
        'image' => $source['image'] ?? '',
        'title' => $source['title'] ?? '',
        'text' => $source['text'] ?? '',
        'background_color' => $source['background_color'] ?? $source['backgroundColor'] ?? $source['bg_color'] ?? '',
        'color' => $source['color'] ?? $source['text_color'] ?? '',
    ];
}
