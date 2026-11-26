<?php

function get_menu()
{
    $menu_s = file_get_contents(__DIR__ . '/../../asset/database/menu.csv');
    $menu_a = explode( "\n", $menu_s);
    $menu_aa = [];
    foreach ( $menu_a as $menu_item_s )
    {
        $menu_aa[] = explode( '|', $menu_item_s );
    }
    return $menu_aa;
}

