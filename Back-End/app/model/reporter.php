<?php

class ReporterModel
{
    public static function getAll(): array
    {
        $q = "SELECT * FROM `t_reporter` ORDER BY id_rep ASC";
        return db_select($q);
    }

    public static function getById(int $id): array
    {
        $q = "SELECT * FROM `t_reporter` WHERE id_rep = :id LIMIT 1";
        $rows = db_select_prepare($q, [ 'id' => (int)$id ]);
        return $rows[0] ?? [];
    }
}

