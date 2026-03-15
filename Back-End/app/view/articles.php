<?php

function html_articles_list(array $articles, array $favoriteIds = []): string
{
    $favoriteIds = array_map('intval', $favoriteIds);
    
    ob_start();
    ?>
    <main>
        <h2 class="mb-3">Tous les articles</h2>
        <?php if (empty($articles)): ?>
            <p>Aucun article disponible.</p>
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
                            <?php if (in_array($id, $favoriteIds, true)): ?>
                                <span class="badge bg-success ms-2">Déjà en favoris</span>
                            <?php else: ?>
                                <form method="post" action="?page=articles" class="d-inline ms-2">
                                    <input type="hidden" name="fav_action" value="add" />
                                    <input type="hidden" name="ident_art" value="<?= $id ?>" />
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Ajouter aux favoris</button>
                                </form>
                            <?php endif; ?>
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

