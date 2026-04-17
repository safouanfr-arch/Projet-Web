<?php

function main_options(): string
{
    controller_handle_global_layout_actions('options');
    $presentation = controller_get_presentation_state();

    // view
    return join("\n", [
        html_head(get_menu(), controller_get_layout_context('options')),
        html_options_page($presentation['font_key'], $presentation['color_key']),
        html_foot(),
    ]);
}

