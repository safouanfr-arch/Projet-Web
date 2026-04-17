<?php

function main_static()
{
    $slug = (string)($_REQUEST['subpage'] ?? '');
    controller_handle_global_layout_actions('static', $slug);
    $static_contents = get_static_contents($slug);

    return join( "\n", [
        html_head(get_menu(), controller_get_layout_context('static', $slug)),
        $static_contents,
        html_foot(),
    ]);

}

