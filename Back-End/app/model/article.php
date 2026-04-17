<?php

class ArticleModel
{
    public static function getLatest(int $limit = 10): array
    {
        $limit = max(1, (int)$limit);
        $q = "SELECT *
              FROM `t_article`
              ORDER BY date_art DESC
              LIMIT $limit";
        return db_select($q);
    }

    public static function getAll(?int $limit = null, int $offset = 0): array
    {
        $offset = max(0, (int)$offset);
        if ($limit !== null) {
            $limit = max(1, (int)$limit);
            $q = "SELECT *
                  FROM `t_article`
                  ORDER BY date_art DESC
                  LIMIT $limit OFFSET $offset";
            return db_select($q);
        }

        $q = "SELECT *
              FROM `t_article`
              ORDER BY date_art DESC";
        return db_select($q);
    }

    public static function getById(int $id): array
    {
        $q = "SELECT *
              FROM `t_article`
              WHERE ident_art = :id";
        $rows = db_select_prepare($q, ['id' => (int)$id]);
        return $rows[0] ?? [];
    }

    public static function search(array $criteria): array
    {
        $where = [];
        $params = [];
        $order = "date_art DESC";

        $keyword = trim((string)($criteria['keyword'] ?? ''));
        if ($keyword !== '') {
            $where[] = "title_art LIKE :kw";
            $params['kw'] = "%$keyword%";
        }

        $categoryId = (int)($criteria['category_id'] ?? 0);
        if ($categoryId > 0) {
            $where[] = "fk_category_art = :cat";
            $params['cat'] = $categoryId;
        }

        $reporterId = (int)($criteria['reporter_id'] ?? 0);
        if ($reporterId > 0) {
            $where[] = "reporter_art = :rep";
            $params['rep'] = $reporterId;
        }

        $whereSql = empty($where) ? '' : ('WHERE ' . implode(' AND ', $where));

        $q = "SELECT *
              FROM `t_article`
              $whereSql
              ORDER BY $order
              LIMIT 10";

        return db_select_prepare($q, $params);
    }

    public static function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $q = "SELECT *
              FROM `t_article`
              WHERE ident_art IN ($placeholders)
              ORDER BY date_art DESC";

        return db_select_prepare($q, $ids);
    }

    public static function getByReadtime(int $readtime, int $limit = 10, array $excludeIds = []): array
    {
        $readtime = max(0, (int)$readtime);
        $limit = max(1, min(10, (int)$limit));

        $excludeIds = array_values(array_filter(array_map('intval', $excludeIds), fn($v) => $v > 0));

        $params = [$readtime];
        $excludeSql = '';
        if (!empty($excludeIds)) {
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $excludeSql = " AND ident_art NOT IN ($placeholders)";
            foreach ($excludeIds as $id) {
                $params[] = $id;
            }
        }

        $q = "SELECT *
              FROM `t_article`
              WHERE readtime_art = ?$excludeSql
              ORDER BY date_art DESC
              LIMIT $limit";

        return db_select_prepare($q, $params);
    }

    public static function getReadtimes(): array
    {
        $q = "SELECT DISTINCT readtime_art
              FROM `t_article`
              WHERE readtime_art IS NOT NULL
              ORDER BY readtime_art ASC";
        $rows = db_select($q);

        $values = [];
        foreach ($rows as $row) {
            $val = (int)($row['readtime_art'] ?? 0);
            if ($val > 0) {
                $values[] = $val;
            }
        }
        return $values;
    }

    public static function getAllByReadtime(int $readtime): array
    {
        $readtime = max(0, (int)$readtime);
        $q = "SELECT *
              FROM `t_article`
              WHERE readtime_art = :rt
              ORDER BY date_art DESC";
        return db_select_prepare($q, ['rt' => $readtime]);
    }

    public static function countByDate(string $date): int
    {
        $q = "SELECT COUNT(*) AS cnt
              FROM `t_article`
              WHERE DATE(date_art) = :date";
        $rows = db_select_prepare($q, ['date' => $date]);
        return (int)($rows[0]['cnt'] ?? 0);
    }
}
