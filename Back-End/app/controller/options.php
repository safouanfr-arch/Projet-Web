<?php

function main_options(): string
{
    // view
    return join("\n", [
        html_head(get_menu()),
        html_options_page(),
        html_foot(),
    ]);
}

