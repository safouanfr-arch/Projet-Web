<?php

function html_head(array $menu_a = [], array $context = []): string
{
    $bodyFontFamily = (string)($context['body_font_family'] ?? 'Arial, sans-serif');
    $bodyTextClass = (string)($context['body_text_class'] ?? 'text-dark');
    $currentPage = (string)($context['current_page'] ?? 'home');
    $currentSubpage = (string)($context['current_subpage'] ?? '');
    $articleClickTotal = (int)($context['article_click_total'] ?? 0);
    $homeMainCompact = !empty($context['home_main_compact']);

    $menu_s = '<ul class="menu nav nav-pills flex-wrap gap-2 mb-0">';
    foreach ($menu_a as $item) {
        $visual = trim((string)($item[0] ?? ''));
        $comp = trim((string)($item[1] ?? 'home'));
        $subcomp = trim((string)($item[2] ?? ''));

        if ($visual === '' && $comp === '') {
            continue;
        }

        $isActive = ($comp === $currentPage) && ($subcomp === '' || $subcomp === $currentSubpage);
        $activeClass = $isActive ? ' active' : '';
        $ariaCurrent = $isActive ? ' aria-current="page"' : '';

        $href = '?page=' . htmlspecialchars($comp);
        if ($subcomp !== '') {
            $href .= '&subpage=' . htmlspecialchars($subcomp);
        }

        $menu_s .= '<li class="nav-item">'
            . '<a class="nav-link rounded-pill px-3' . $activeClass . '"' . $ariaCurrent . ' href="' . $href . '">'
            . htmlspecialchars($visual)
            . '</a>'
            . '</li>';
    }
    $menu_s .= '</ul>';

    $headerIconWebPath = './icon/icon3.png';
    $headerIconFsPath = __DIR__ . '/../../public/icon/icon3.png';
    $isHome = ($currentPage === 'home');
    $currentPageUrl = '?page=' . htmlspecialchars($currentPage);
    if ($currentSubpage !== '') {
        $currentPageUrl .= '&subpage=' . htmlspecialchars($currentSubpage);
    }

    $mainCssPath = __DIR__ . '/../../public/css/internal/main.css';
    $mainCssVersion = is_file($mainCssPath) ? (string)filemtime($mainCssPath) : (string)time();

    ob_start();
    ?>
    <html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>France 24 (MVC)</title>

        <link rel="stylesheet" href="./css/bootstrap/bootstrap.min.css" />
        <link rel="stylesheet" href="./css/internal/main.css?v=<?= htmlspecialchars($mainCssVersion) ?>" />

    </head>
    <body class="<?= htmlspecialchars($bodyTextClass) ?>" style="font-family: <?= htmlspecialchars($bodyFontFamily) ?>;">
    <div class="container-fluid py-3">
    <header class="mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <a class="text-decoration-none" href="?page=home">
                    <h1 class="h4 m-0">France 24 (MVC)</h1>
                </a>
                <?php if (is_file($headerIconFsPath)): ?>
                    <img src="<?= htmlspecialchars($headerIconWebPath) ?>" alt="" style="width:40px;height:auto;">
                <?php endif; ?>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="small">Clics sur les articles : <?= $articleClickTotal ?></span>
                <form method="post" action="<?= $currentPageUrl ?>" class="m-0">
                    <input type="hidden" name="click_action" value="reset" />
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Réinitialiser</button>
                </form>

                <?php if ($isHome): ?>
                    <form method="post" action="?page=home" class="m-0">
                        <input type="hidden" name="home_action" value="toggle_main_compact" />
                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                            <?= $homeMainCompact ? 'Afficher détails' : 'Titres seuls' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <nav class="mt-3">
            <?= $menu_s ?>
        </nav>
    </header>
    <?php
    return ob_get_clean();
}

function html_foot()
{
    $footerIconWebPath = './icon/awebwiz.png';
    $footerIconFsPath = __DIR__ . '/../../public/icon/awebwiz.png';

    ob_start();
    ?>
    <footer class="mt-5 pt-3 border-top">
        <div class="d-flex align-items-center gap-2 small">
            <span>Made with the amazing AWebWiz framework</span>
            <?php if (is_file($footerIconFsPath)): ?>
                <img src="<?= htmlspecialchars($footerIconWebPath) ?>" alt="AWebWiz logo" style="width:32px;height:auto;">
            <?php endif; ?>
        </div>
    </footer>
    </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}
