<?php

function main_readtime(): string
{
    $selected = (int)($_REQUEST['readtime'] ?? 0);

    // model
    $readtimes = ArticleModel::getReadtimes();
    $articles = [];
    if ($selected > 0) {
        $articles = ArticleModel::getAllByReadtime($selected);
    }

    // view
    return join("\n", [
        html_head(get_menu()),
        html_readtime_page($readtimes, $selected, $articles),
        html_foot(),
    ]);
}
