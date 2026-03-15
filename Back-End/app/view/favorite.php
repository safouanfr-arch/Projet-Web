<?php

function html_favorites_page(array $articles): string
{
    $count = count($articles);

    ob_start();
    ?>
    <main>
        <h2 class="mb-2">Mes favoris</h2>
        <p class="text-muted">Total : <strong><?= $count ?></strong> article(s) favori(s)</p>
        
        <?php if (empty($articles)): ?>
            <p>Vous n'avez aucun article favori.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($articles as $art): ?>
                    <?php $id = (int)($art['ident_art'] ?? 0); ?>
                    <li class="list-group-item">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($art['title_art'] ?? '') ?></h5>
                                <p class="mb-2"><?= htmlspecialchars($art['hook_art'] ?? '') ?></p>
                                <div class="small text-muted">
                            <?= htmlspecialchars($art['date_art'] ?? '') ?> | 
                            Temps de lecture : <?= (int)($art['readtime_art'] ?? 0) ?> min
                                </div>
                                <a class="btn btn-sm btn-outline-secondary mt-2" href="?page=article&ident_art=<?= $id ?>">Lire l'article</a>
                            </div>

                            <div>

                        <?php if ($id > 0): ?>
                            <form method="post" action="?page=favorite" class="d-inline ms-2">
                                <input type="hidden" name="fav_action" value="remove" />
                                <input type="hidden" name="ident_art" value="<?= $id ?>" />
                                <button type="submit" class="btn btn-sm btn-outline-danger">Retirer</button>
                            </form>
                        <?php endif; ?>

                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
    <?php
    return ob_get_clean();
}

