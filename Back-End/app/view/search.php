<?php

function html_search_page(array $criteria, array $categories, array $reporters, array $results): string
{
    $keyword = htmlspecialchars((string)($criteria['keyword'] ?? ''));
    $categoryId = (int)($criteria['category_id'] ?? 0);
    $reporterId = (int)($criteria['reporter_id'] ?? 0);

    ob_start();
    ?>
    <main>
        <h2 class="mb-3">Recherche</h2>

        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="?page=search" class="mb-0">
                    <input type="hidden" name="page" value="search">

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Mot-clé</label>
                            <input class="form-control" name="keyword" type="text" value="<?= $keyword ?>">
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label">Catégorie</label>
                            <select class="form-select" name="category_id">
                                <option value="0">Toutes</option>
                                <?php foreach ($categories as $cat): ?>
                                    <?php
                                        // tolérant si les colonnes ne s'appellent pas identiquement partout
                                        $id = (int)($cat['ident_cat'] ?? $cat['id_cat'] ?? $cat['category_id'] ?? $cat['id'] ?? 0);
                                        $rawName = (string)($cat['name_cat'] ?? $cat['label_cat'] ?? $cat['title_cat'] ?? $cat['name'] ?? $cat['label'] ?? '');
                                        $name = htmlspecialchars($rawName);
                                        $selected = ($id !== 0 && $id === $categoryId) ? 'selected' : '';
                                    ?>
                                    <option value="<?= $id ?>" <?= $selected ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label">Auteur</label>
                            <select class="form-select" name="reporter_id">
                                <option value="0">Tous</option>
                                <?php foreach ($reporters as $rep): ?>
                                    <?php
                                        $id = (int)($rep['ident_rep'] ?? $rep['id_rep'] ?? $rep['reporter_id'] ?? $rep['id'] ?? 0);

                                        $first = trim((string)($rep['firstname_rep'] ?? $rep['first_name'] ?? $rep['firstname'] ?? ''));
                                        $last = trim((string)($rep['lastname_rep'] ?? $rep['last_name'] ?? $rep['lastname'] ?? ''));
                                        $fallbackName = (string)($rep['name_rep'] ?? $rep['label_rep'] ?? $rep['name'] ?? $rep['label'] ?? '');

                                        $rawName = trim($first . ' ' . $last);
                                        if ($rawName === '') {
                                            $rawName = $fallbackName;
                                        }

                                        $name = htmlspecialchars($rawName);
                                        $selected = ($id !== 0 && $id === $reporterId) ? 'selected' : '';
                                    ?>
                                    <option value="<?= $id ?>" <?= $selected ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </div>
                </form>
            </div>
        </div>

        <h3 class="h5 mb-3">Résultats</h3>
        <?php if (empty($results)): ?>
            <p class="text-muted">Aucun résultat pour ces critères.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($results as $art): ?>
                    <?php
                        $title = htmlspecialchars((string)($art['title_art'] ?? ''));
                        $hook = htmlspecialchars((string)($art['hook_art'] ?? ''));
                        $date = htmlspecialchars((string)($art['date_art'] ?? ''));
                        $id = (int)($art['ident_art'] ?? 0);
                    ?>
                    <li class="list-group-item">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                            <div>
                                <h5 class="mb-1"><?= $title ?></h5>
                                <?php if ($date !== ''): ?>
                                    <div class="small text-muted mb-2"><?= $date ?></div>
                                <?php endif; ?>
                                <?php if ($hook !== ''): ?>
                                    <p class="mb-0"><?= $hook ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a class="btn btn-sm btn-outline-secondary" href="?page=article&ident_art=<?= $id ?>">Lire l'article</a>
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
