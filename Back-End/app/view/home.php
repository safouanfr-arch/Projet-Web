<?php

function html_home_page($featured, array $main, array $secondary, array $readtime3): string
{
	$mainCompact = (string)($_COOKIE['home_main_compact'] ?? '') === '1';
	$secondaryCols = (int)($_COOKIE['home_secondary_cols'] ?? 3);

	ob_start();
	?>
	<main>
		<h2 class="mb-3">Accueil</h2>

		<!-- Article phare (le plus récent) -->
		<?php if ($featured): ?>
		<section class="featured-article mb-4">
			<div class="card">
				<div class="card-body">
					<div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
						<div>
							<h3 class="h5 mb-2">Article phare</h3>
							<h4 class="h4 mb-2"><?= htmlspecialchars($featured['title_art'] ?? '') ?></h4>
							<p class="mb-2"><strong><?= htmlspecialchars($featured['hook_art'] ?? '') ?></strong></p>
							<div class="small text-muted"><?= htmlspecialchars($featured['date_art'] ?? '') ?> | <?= (int)($featured['readtime_art'] ?? 0) ?> min</div>
						</div>
						<div>
							<a class="btn btn-outline-secondary" href="?page=article&ident_art=<?= (int)$featured['ident_art'] ?>">Lire l'article</a>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- Articles principaux (3) -->
		<?php if (!empty($main)): ?>
		<section class="main-articles mb-4">
			<div class="card">
				<div class="card-body">
					<h3 class="h5 mb-3">Articles principaux</h3>
					<ul class="list-group">
				<?php foreach ($main as $art): ?>
				<li class="list-group-item">
					<?php
						$title = htmlspecialchars($art['title_art'] ?? '');
						$hook = htmlspecialchars($art['hook_art'] ?? '');
						$date = htmlspecialchars($art['date_art'] ?? '');
						$readtime = (int)($art['readtime_art'] ?? 0);
						$image = htmlspecialchars($art['image_art'] ?? '');
						$link = '?page=article&ident_art=' . (int)($art['ident_art'] ?? 0);
					?>

					<h5 class="mb-1"><a class="text-decoration-none" href="<?= $link ?>"><?= $title ?></a></h5>

					<?php if (!$mainCompact): ?>
						<?php if (!empty($image)): ?>
							<div class="main-article-image">
								<img class="img-fluid rounded" src="<?= MEDIA_PATH . $image ?>" alt="<?= $title ?>">
							</div>
						<?php endif; ?>

						<?php if (!empty($hook)): ?>
							<p class="mb-2"><?= $hook ?></p>
						<?php endif; ?>
						<div class="small text-muted"><?= $date ?> | <?= $readtime ?> min</div>
					<?php endif; ?>

					<div class="mt-2">
						<a class="btn btn-sm btn-outline-secondary" href="<?= $link ?>">Lire l'article</a>
					</div>
				</li>
				<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- Articles secondaires (6) -->
		<?php if (!empty($secondary)): ?>
		<section class="secondary-articles mb-4">
			<div class="card">
				<div class="card-body">
					<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
						<h3 class="h5 m-0">Articles secondaires</h3>
						<form method="post" action="?page=home" class="secondary-cols m-0">
							<input type="hidden" name="home_action" value="set_secondary_cols" />
							<div class="d-flex align-items-end gap-2">
								<div>
									<label class="form-label mb-0">Colonnes</label>
									<select class="form-select form-select-sm" name="home_secondary_cols">
										<option value="2" <?= ($secondaryCols === 2) ? 'selected' : '' ?>>2</option>
										<option value="3" <?= ($secondaryCols === 3) ? 'selected' : '' ?>>3</option>
										<option value="4" <?= ($secondaryCols === 4) ? 'selected' : '' ?>>4</option>
									</select>
								</div>
								<button class="btn btn-sm btn-outline-secondary" type="submit">Appliquer</button>
							</div>
						</form>
					</div>

					<ul class="secondary-grid cols-<?= (int)$secondaryCols ?>">
				<?php foreach ($secondary as $art): ?>
				<li>
					<div class="card h-100">
						<div class="card-body">
							<h5 class="mb-1"><?= htmlspecialchars($art['title_art'] ?? '') ?></h5>
							<p class="mb-2"><?= htmlspecialchars($art['hook_art'] ?? '') ?></p>
							<div class="small text-muted mb-2"><?= htmlspecialchars($art['date_art'] ?? '') ?> | <?= (int)($art['readtime_art'] ?? 0) ?> min</div>
							<a class="btn btn-sm btn-outline-secondary" href="?page=article&ident_art=<?= (int)$art['ident_art'] ?>">Lire</a>
						</div>
					</div>
				</li>
				<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- Articles (3 min) - liste séparée tout en bas -->
		<section class="readtime-3-articles mb-4">
			<div class="card">
				<div class="card-body">
					<h3 class="h5 mb-3">Articles (3 min)</h3>
			<?php if (empty($readtime3)): ?>
				<p class="text-muted mb-0">Aucun article de 3 minutes.</p>
			<?php else: ?>
				<ul class="list-group">
					<?php foreach ($readtime3 as $art): ?>
						<?php
							$title = htmlspecialchars((string)($art['title_art'] ?? ''));
							$date = htmlspecialchars((string)($art['date_art'] ?? ''));
							$id = (int)($art['ident_art'] ?? 0);
							$link = '?page=article&ident_art=' . $id;
						?>
						<li class="list-group-item">
							<a class="text-decoration-none" href="<?= $link ?>"><?= $title ?></a>
							<?php if ($date !== ''): ?>
								<span class="small text-muted"> — <?= $date ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
				</div>
			</div>
		</section>
	</main>
	<?php
	return ob_get_clean();
}