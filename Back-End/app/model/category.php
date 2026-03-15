<?php

class CategoryModel
{
    public static function getAll(): array
    {
        $q = "SELECT * FROM `t_category` ORDER BY name_cat ASC";
        return db_select($q);
    }
}

