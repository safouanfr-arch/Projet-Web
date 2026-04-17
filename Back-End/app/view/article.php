<?php

function html_article_detail(array $article): string
{
    if (empty($article)) {
        return '<main><p>Article non trouvé.</p></main>';
    }

    $title = htmlspecialchars($article['title_art'] ?? '');
    $hook = htmlspecialchars($article['hook_art'] ?? '');
    $content = sanitize_article_html((string)($article['content_art'] ?? ''));
    $date = htmlspecialchars($article['date_art'] ?? '');
    $readtime = (int)($article['readtime_art'] ?? 0);
    $image = htmlspecialchars($article['image_art'] ?? '');

    $media_src = MEDIA_PATH . $image;

    ob_start();
    ?>
    <main>
        <article class="article-detail">
            <h2 class="mb-2"><?= $title ?></h2>
            <?php if ($hook !== ''): ?>
                <p class="lead mb-2"><strong><?= $hook ?></strong></p>
            <?php endif; ?>
            <div class="small text-muted">
                Publié le <?= $date ?> | Temps de lecture : <?= $readtime ?> min
            </div>
            <?php if ($image): ?>
                <div class="article-image">
                    <img class="img-fluid rounded my-3" src="<?= $media_src ?>" alt="<?= $title ?>">
                </div>
            <?php endif; ?>
            <div class="article-content mt-3">
                <?= $content ?>
            </div>
        </article>
    </main>
    <?php
    return ob_get_clean();
}

