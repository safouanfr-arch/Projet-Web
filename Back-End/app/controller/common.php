<?php

function controller_presentation_font_map(): array
{
    return [
        'arial' => 'Arial, sans-serif',
        'times' => '"Times New Roman", Times, serif',
        'consolas' => 'Consolas, "Courier New", monospace',
    ];
}

function controller_presentation_color_map(): array
{
    return [
        'noir' => 'text-dark',
        'bleu' => 'text-primary',
        'rouge' => 'text-danger',
    ];
}

function controller_normalize_presentation_font_key(string $fontKey): string
{
    $fonts = controller_presentation_font_map();
    return array_key_exists($fontKey, $fonts) ? $fontKey : 'arial';
}

function controller_normalize_presentation_color_key(string $colorKey): string
{
    $colors = controller_presentation_color_map();
    return array_key_exists($colorKey, $colors) ? $colorKey : 'noir';
}

function controller_page_url(string $page, string $subpage = ''): string
{
    $url = '?page=' . rawurlencode($page);
    if ($subpage !== '') {
        $url .= '&subpage=' . rawurlencode($subpage);
    }
    return $url;
}

function controller_handle_global_layout_actions(string $page, string $subpage = ''): void
{
    if (($_POST['click_action'] ?? '') === 'reset') {
        unset($_SESSION['article_click_total']);
        header('Location: ' . controller_page_url($page, $subpage));
        exit;
    }

    if (($_POST['presentation_action'] ?? '') === 'save') {
        $fontKey = controller_normalize_presentation_font_key((string)($_POST['presentation_font'] ?? ''));
        $colorKey = controller_normalize_presentation_color_key((string)($_POST['presentation_color'] ?? ''));

        $expire = time() + (60 * 60 * 24 * 30);
        setcookie('presentation_font', $fontKey, $expire, '/');
        setcookie('presentation_color', $colorKey, $expire, '/');

        header('Location: ' . controller_page_url($page, $subpage));
        exit;
    }
}

function controller_get_presentation_state(): array
{
    $fonts = controller_presentation_font_map();
    $colors = controller_presentation_color_map();

    $fontKey = controller_normalize_presentation_font_key((string)($_COOKIE['presentation_font'] ?? 'arial'));
    $colorKey = controller_normalize_presentation_color_key((string)($_COOKIE['presentation_color'] ?? 'noir'));

    return [
        'font_key' => $fontKey,
        'color_key' => $colorKey,
        'body_font_family' => $fonts[$fontKey],
        'body_text_class' => $colors[$colorKey],
    ];
}

function controller_get_home_preferences(): array
{
    $secondaryCols = (int)($_COOKIE['home_secondary_cols'] ?? 3);
    if (!in_array($secondaryCols, [2, 3, 4], true)) {
        $secondaryCols = 3;
    }

    return [
        'main_compact' => (string)($_COOKIE['home_main_compact'] ?? '') === '1',
        'secondary_cols' => $secondaryCols,
    ];
}

function controller_get_layout_context(string $page, string $subpage = ''): array
{
    $presentation = controller_get_presentation_state();
    $homePreferences = controller_get_home_preferences();

    return array_merge($presentation, [
        'current_page' => $page,
        'current_subpage' => $subpage,
        'article_click_total' => (int)($_SESSION['article_click_total'] ?? 0),
        'home_main_compact' => $homePreferences['main_compact'],
    ]);
}
