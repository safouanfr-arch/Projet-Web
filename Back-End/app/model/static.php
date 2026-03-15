<?php

function get_static_contents($filename)
{
    $slug = trim((string)$filename);

    if ($slug === '') {
        return '<main><p>Page statique non spécifiée.</p></main>';
    }

    // slug simple (ex: about, cgv)
    if (!preg_match('/^[a-z0-9_-]+$/i', $slug)) {
        return '<main><p>Page statique invalide.</p></main>';
    }

    $q = "SELECT content_sta
          FROM `t_static`
          WHERE slug_sta = :slug
          LIMIT 1";

    $rows = db_select_prepare($q, [ 'slug' => $slug ]);
    $html = (string)($rows[0]['content_sta'] ?? '');

    if ($html === '') {
        return '<main><p>Page statique introuvable.</p></main>';
    }

    return $html;
}

