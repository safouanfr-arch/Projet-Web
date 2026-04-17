<?php

function main_api_login(): string
{
    $login = trim((string)($_POST['login'] ?? ''));
    $password = trim((string)($_POST['password'] ?? ''));

    if ($login === '' || $password === '') {
        return api_json_response(['success' => false, 'error' => 'Login et mot de passe requis'], 400);
    }

    $data = api_remote_json_post_form(
        'http://playground.burotix.be/login',
        [
            'login' => $login,
            'password' => $password,
        ]
    );

    if ($data === null) {
        return api_json_response(['success' => false, 'error' => 'Impossible de contacter le serveur d\'authentification'], 502);
    }

    if (!empty($data['error'])) {
        return api_json_response(['success' => false, 'error' => (string)$data['error']], 401);
    }

    $role = $data['role'] ?? $data['group'] ?? null;
    if (!$role) {
        $role = (strtolower($login) === 'admin') ? 'admin' : 'user';
    }

    $_SESSION['user'] = [
        'name' => $data['name'] ?? $data['username'] ?? $data['login'] ?? $login,
        'role' => strtolower((string)$role),
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
