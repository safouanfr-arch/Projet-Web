<?php

function html_readtime_page(array $readtimes, int $selectedReadtime, array $articles): string
{
    ob_start();
    ?>
    <main>
        <h2 class="mb-3">Temps de lecture</h2>

        <?php if (empty($readtimes)): ?>
            <p>Aucun temps de lecture disponible.</p>
        <?php else: ?>
            <h3>Liste des temps</h3>
            <ul class="list-group mb-3">
                <?php foreach ($readtimes as $rt): ?>
                    <?php $rt = (int)$rt; ?>
                    <li class="list-group-item">
                        <a class="text-decoration-none" href="?page=readtime&readtime=<?= $rt ?>"><?= $rt ?> min</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($selectedReadtime > 0): ?>
            <hr />
            <h3>Articles — <?= (int)$selectedReadtime ?> min</h3>

            <?php if (empty($articles)): ?>
                <p>Aucun article pour ce temps de lecture.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($articles as $art): ?>
                        <?php
                            $id = (int)($art['ident_art'] ?? 0);
                            $title = htmlspecialchars((string)($art['title_art'] ?? ''));
                            $date = htmlspecialchars((string)($art['date_art'] ?? ''));
                        ?>
                        <li class="list-group-item">
                            <a class="text-decoration-none" href="?page=article&ident_art=<?= $id ?>"><?= $title ?></a>
                            <?php if ($date !== ''): ?>
                                <small> — <?= $date ?></small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>

    </main>
    <?php
    return ob_get_clean();
}
