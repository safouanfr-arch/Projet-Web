<?php

function main_api_login(): string
{
    $login = trim((string)($_POST['login'] ?? ''));
    $password = trim((string)($_POST['password'] ?? ''));

    if ($login === '' || $password === '') {
        return api_json_response(['success' => false, 'error' => 'Login et mot de passe requis'], 400);
    }

    if (getenv('PRESS_DEMO') === '1') {
        if (!hash_equals('reader', $login) || !hash_equals('demo', $password)) {
            return api_json_response(['success' => false, 'error' => 'Identifiants incorrects'], 401);
        }
        $data = ['name' => 'reader', 'role' => 'user'];
    } else {
        $url = (string)getenv('PRESS_AUTH_URL');
        if (parse_url($url, PHP_URL_SCHEME) !== 'https') {
            return api_json_response(['success' => false, 'error' => 'Connexion indisponible'], 503);
        }
        $data = api_remote_json_post_form($url, ['login' => $login, 'password' => $password]);
        if ($data === null) {
            return api_json_response(['success' => false, 'error' => 'Service indisponible'], 502);
        }
        if (!empty($data['error']) || empty($data['name'] ?? $data['username'] ?? $data['login'])) {
            return api_json_response(['success' => false, 'error' => 'Identifiants incorrects'], 401);
        }
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'name' => $data['name'] ?? $data['username'] ?? $data['login'] ?? $login,
        'role' => strtolower((string)($data['role'] ?? $data['group'] ?? 'user')),
    ];

    return api_json_response([
        'success' => true,
        'user' => $_SESSION['user'],
    ]);
}

function main_api_logout(): string
{
    unset($_SESSION['user']);

    return api_json_response(['success' => true]);
}

function main_api_session(): string
{
    $favorites = api_get_favorite_ids();

    return api_json_response([
        'success' => true,
        'logged_in' => isset($_SESSION['user']),
        'user' => $_SESSION['user'] ?? null,
        'favorite_count' => count($favorites),
    ]);
}
