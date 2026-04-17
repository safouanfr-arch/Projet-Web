<?php

function main_readtime(): string
{
    controller_handle_global_layout_actions('readtime');

    $selected = (int)($_REQUEST['readtime'] ?? 0);

    // model
    $readtimes = ArticleModel::getReadtimes();
    $articles = [];
    if ($selected > 0) {
        $articles = ArticleModel::getAllByReadtime($selected);
    }

    // view
    return join("\n", [
        html_head(get_menu(), controller_get_layout_context('readtime')),
        html_readtime_page($readtimes, $selected, $articles),
        html_foot(),
    ]);
}
