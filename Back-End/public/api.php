<?php
/**
 * api.php - Point d'entrée unique pour les requêtes fetch (Livrable 3)
 * Dispatche selon $_REQUEST['action']
 * Renvoie toujours du JSON
 */

// CORS headers pour le dev server Vite (localhost:5173 ou autre port)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (preg_match('/^http:\/\/localhost(:\d+)?$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: http://localhost:5173');
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Charger la config et les modèles
require_once "../app/config/app.php";
require_once "../app/config/model.php";

// Charger tous les fichiers modèle
foreach (scandir(ROOT_DIR . 'model') as $file) {
    if (substr($file, -4) === '.php') {
        require_once ROOT_DIR . 'model' . DIRECTORY_SEPARATOR . $file;
    }
}

session_start();

// Dispatch
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    // ─── BLOC A : Détail d'un article (délégué au controller detail_fetch.php) ───
    case 'detail':
        require_once ROOT_DIR . 'controller' . DIRECTORY_SEPARATOR . 'detail_fetch.php';
        echo json_encode(controller_detail_fetch());
        break;

    // ─── BLOC A : Liste d'articles ───
    case 'articles':
        $limit = isset($_REQUEST['limit']) ? (int)$_REQUEST['limit'] : null;
        $offset = (int)($_REQUEST['offset'] ?? 0);
        $articles = ArticleModel::getAll($limit, $offset);
        echo json_encode(['success' => true, 'articles' => $articles, 'count' => count($articles)]);
        break;

    // ─── BLOC B : Ajouter un favori ───
    case 'favorite_add':
        $id = (int)($_REQUEST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID invalide']);
            break;
        }
        $favorites = $_SESSION['favorites'] ?? [];
        if (!is_array($favorites)) $favorites = [];
        if (!in_array($id, $favorites)) {
            $favorites[] = $id;
        }
        $_SESSION['favorites'] = $favorites;
        echo json_encode(buildFavoriteResponse($id));
        break;

    // ─── BLOC B : Retirer un favori ───
    case 'favorite_remove':
        $id = (int)($_REQUEST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID invalide']);
            break;
        }
        $favorites = $_SESSION['favorites'] ?? [];
        if (!is_array($favorites)) $favorites = [];
        $favorites = array_values(array_diff($favorites, [$id]));
        $_SESSION['favorites'] = $favorites;
        echo json_encode(buildFavoriteResponse($id));
        break;

    // ─── BLOC B : Vider tous les favoris ───
    case 'favorite_clear':
        $_SESSION['favorites'] = [];
        echo json_encode(['success' => true, 'favorites' => [], 'count' => 0]);
        break;

    // ─── BLOC B : Liste des favoris ───
    case 'favorite_list':
        $favorites = $_SESSION['favorites'] ?? [];
        if (!is_array($favorites)) $favorites = [];
        $favorites = array_map('intval', $favorites);
        $articles = empty($favorites) ? [] : ArticleModel::getByIds($favorites);
        echo json_encode([
            'success'   => true,
            'articles'  => $articles,
            'ids'       => $favorites,
            'count'     => count($favorites),
        ]);
        break;

    // ─── BLOC B : Vérifier si un article est favori ───
    case 'favorite_check':
        $id = (int)($_REQUEST['id'] ?? 0);
        $favorites = $_SESSION['favorites'] ?? [];
        if (!is_array($favorites)) $favorites = [];
        echo json_encode([
            'success'     => true,
            'is_favorite' => in_array($id, $favorites),
        ]);
        break;

    // ─── BLOC C : Recherche ───
    case 'search':
        $criteria = [
            'keyword'     => trim((string)($_REQUEST['keyword'] ?? '')),
            'category_id' => (int)($_REQUEST['category_id'] ?? 0),
            'reporter_id' => (int)($_REQUEST['reporter_id'] ?? 0),
        ];
        $hasCriteria = (
            $criteria['keyword'] !== '' ||
            $criteria['category_id'] > 0 ||
            $criteria['reporter_id'] > 0
        );
        $results    = $hasCriteria ? ArticleModel::search($criteria) : ArticleModel::getLatest(10);
        $categories = CategoryModel::getAll();
        $reporters  = ReporterModel::getAll();
        echo json_encode([
            'success'    => true,
            'results'    => $results,
            'count'      => count($results),
            'categories' => $categories,
            'reporters'  => $reporters,
        ]);
        break;

    // ─── BLOC D : Login via API distante ───
    case 'login':
        $login    = trim((string)($_POST['login'] ?? ''));
        $password = trim((string)($_POST['password'] ?? ''));
        if ($login === '' || $password === '') {
            echo json_encode(['success' => false, 'error' => 'Login et mot de passe requis']);
            break;
        }
        // Appel à l'API distante (playground.burotix.be)
        $postData = http_build_query(['login' => $login, 'password' => $password]);
        $context  = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $postData,
                'timeout' => 10,
            ],
        ]);
        $response = @file_get_contents('http://playground.burotix.be/login', false, $context);
        if ($response === false) {
            echo json_encode(['success' => false, 'error' => 'Impossible de contacter le serveur d\'authentification']);
            break;
        }
        $data = json_decode($response, true);
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Réponse invalide du serveur distant']);
            break;
        }
        // Vérifier si l'API distante signale une erreur explicite
        // L'API playground.burotix.be retourne seulement 'error' en cas d'échec
        if (!empty($data['error'])) {
            echo json_encode(['success' => false, 'error' => $data['error']]);
            break;
        }

        // Déterminer le rôle:
        // 1. Depuis la réponse API si fourni
        // 2. Sinon, dériver du login: 'admin' → role admin, autre → role user
        $role = $data['role'] ?? $data['group'] ?? null;
        if (!$role) {
            $role = (strtolower($login) === 'admin') ? 'admin' : 'user';
        }

        // Stocker utilisateur en session avec rôle normalisé
        $_SESSION['user'] = [
            'name' => $data['name'] ?? $data['username'] ?? $data['login'] ?? $login,
            'role' => strtolower($role),
        ];
        echo json_encode([
            'success' => true,
            'user'    => $_SESSION['user'],
        ]);
        break;

    // ─── BLOC D : Déconnexion ───
    case 'logout':
        unset($_SESSION['user']);
        echo json_encode(['success' => true]);
        break;

    // ─── BLOC D : État de la session ───
    case 'session':
        $favorites = $_SESSION['favorites'] ?? [];
        if (!is_array($favorites)) $favorites = [];
        echo json_encode([
            'success'        => true,
            'logged_in'      => isset($_SESSION['user']),
            'user'           => $_SESSION['user'] ?? null,
            'favorite_count' => count($favorites),
        ]);
        break;

    // ─── BLOC E : Bannière publicitaire ───
    case 'banner':
        $response = @file_get_contents('http://playground.burotix.be/adv/banner_for_isfce.json');
        if ($response === false) {
            echo json_encode(['success' => false, 'error' => 'Impossible de charger la bannière']);
            break;
        }
        $banner = json_decode($response, true);
        if (!$banner) {
            echo json_encode(['success' => false, 'error' => 'Données bannière invalides']);
            break;
        }
        echo json_encode(['success' => true, 'banner' => $banner]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Action inconnue: ' . $action]);
        break;
}

/**
 * Construit la réponse JSON standard pour les actions favori
 */
function buildFavoriteResponse(int $checkedId): array
{
    $favorites = $_SESSION['favorites'] ?? [];
    if (!is_array($favorites)) $favorites = [];

    // Récupérer les titres (max 5)
    $articles = empty($favorites) ? [] : ArticleModel::getByIds($favorites);
    $titles = array_slice(array_column($articles, 'title_art'), 0, 5);

    return [
        'success'     => true,
        'favorites'   => $titles,
        'count'       => count($favorites),
        'is_favorite' => in_array($checkedId, $favorites),
    ];
}
