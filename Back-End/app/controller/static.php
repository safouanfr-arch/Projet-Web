<?php

function main_static()
{
    $slug = (string)($_REQUEST['subpage'] ?? '');
    $static_contents = get_static_contents($slug);

    return join( "\n", [
        html_head(get_menu()),
        $static_contents,
        html_foot(),
    ]);

}

