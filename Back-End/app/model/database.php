<?php

function db_get_pdo()
{
    static $pdo;
    // echo "PDO " . __LINE__;

    if( empty($pdo)  )
    {
        // echo "PDO " . __LINE__;
        $pdo = new PDO( DATABASE_DSN, DATABASE_USERNAME, DATABASE_PASSWORD );
        //new PDO("mysql:host=localhost;dbname=press_2025", "root", "");
    }

    // echo "PDO " . __LINE__;
    return $pdo;
}


/**
 * execute une requête SQL de type SELECT et retourne un assoc array
 * @param $query_s string avec l'instruction SQL
 * @return array
 */
function db_select($query_s): array
{
    $pdo = db_get_pdo();

    // requête avec les titres des articles
    $stmt = $pdo->query($query_s);  //“Connexion à la base de données, exécute cette requête SQL.”
    $content_a  = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($content_a);

    return $content_a;
}

/**
 * execute une requête SQL préparée de type SELECT et retourne un assoc array
 * @param $query_s string avec l'instruction SQL
 *
 * @return array
 */
function db_select_prepare($query_s, $param_a): array
{
    $pdo = db_get_pdo();

    // $stmt = $pdo->query($query_s);
    $stmt = $pdo->prepare($query_s);
    $stmt->execute($param_a);
    $content_a  = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($content_a);

    return $content_a;
}

/**
 * simple DB check: count articles
 * @return int
 */
function db_count_articles(): int
{
    $q = "SELECT COUNT(*) AS cnt FROM `t_article`";
    $content_a = db_select($q);
    return (int)($content_a[0]['cnt'] ?? 0);
}

/**
 * simple DB check: count categories
 * @return int
 */
function db_count_categories(): int
{
    $q = "SELECT COUNT(*) AS cnt FROM `t_category`";
    $content_a = db_select($q);
    return (int)($content_a[0]['cnt'] ?? 0);
}

/**
 * simple DB check: count reporters
 * @return int
 */
function db_count_reporters(): int
{
    $q = "SELECT COUNT(*) AS cnt FROM `t_reporter`";
    $content_a = db_select($q);
    return (int)($content_a[0]['cnt'] ?? 0);
}



