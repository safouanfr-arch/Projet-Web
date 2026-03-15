<?php

function html_options_page(): string
{
    $allowedFonts = [
        'arial' => 'Arial',
        'times' => 'Times',
        'consolas' => 'Consolas',
    ];

    $allowedColors = [
        'noir' => 'Noir',
        'bleu' => 'Bleu',
        'rouge' => 'Rouge',
    ];

    $fontKey = (string)($_COOKIE['presentation_font'] ?? 'arial');
    $colorKey = (string)($_COOKIE['presentation_color'] ?? 'noir');

    if (!array_key_exists($fontKey, $allowedFonts)) {
        $fontKey = 'arial';
    }
    if (!array_key_exists($colorKey, $allowedColors)) {
        $colorKey = 'noir';
    }

    ob_start();
    ?>
    <main>
        <h2 class="mb-3">Options</h2>

        <div class="card">
            <div class="card-body">
                <h3 class="h5 mb-3">Présentation</h3>
                <form method="post" class="mb-0">
                    <input type="hidden" name="presentation_action" value="save" />

                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Famille de police</label>
                            <select class="form-select" name="presentation_font">
                                <option value="arial" <?= ($fontKey === 'arial') ? 'selected' : '' ?>>Arial</option>
                                <option value="times" <?= ($fontKey === 'times') ? 'selected' : '' ?>>Times</option>
                                <option value="consolas" <?= ($fontKey === 'consolas') ? 'selected' : '' ?>>Consolas</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label">Couleur de la police</label>
                            <select class="form-select" name="presentation_color">
                                <option value="noir" <?= ($colorKey === 'noir') ? 'selected' : '' ?>>Noir</option>
                                <option value="bleu" <?= ($colorKey === 'bleu') ? 'selected' : '' ?>>Bleu</option>
                                <option value="rouge" <?= ($colorKey === 'rouge') ? 'selected' : '' ?>>Rouge</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Appliquer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php
    return ob_get_clean();
}

