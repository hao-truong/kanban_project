<?php
declare(strict_types=1);

namespace app\models;

use app\core\Model;
use PDO;

class ColumnModel extends Model implements IModel
{
    private array $ALLOW_FIELDS = [
        'id',
        'creator_id',
        'board_id',
    ];

    /**
     * @param array $entity
     * @return array
     */
    public function save(array $entity): array
    {
        $this->beginTransaction();
        $query_sql = "insert into columns (title, creator_id, board_id, position) values (:title, :creator_id, :board_id, :position)";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            $entity
        );
        $this->commit();
        $last_insert_id = $this->database->getConnection()->lastInsertId();
        return $this->findOne('id', $last_insert_id);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @return array|null
     */
    public function findOne(mixed $field, mixed $value): array|null
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return null;
        }

        $query_sql = sprintf("select * from columns where %s = :value", $field);
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * @param array $entity
     * @return array
     */
    public function update(array $entity): array
    {
        $this->beginTransaction();
        $query_sql = "UPDATE columns SET title = :title, created_at = :created_at, updated_at = :updated_at, creator_id = :creator_id, board_id = :board_id, position = :position WHERE id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute($entity);
        $this->commit();
        return $this->findOne('id', strval($entity['id']));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function find(string $field, mixed $value): array
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return [];
        }

        $query_sql = sprintf("select * from columns where %s = :value order by position ASC", $field);
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }

    public function deleteById(mixed $id): void
    {
        $this->beginTransaction();
        $query_sql = "delete from columns where id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "id" => $id,
            ]
        );
        $this->commit();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function delete(string $field, mixed $value): void
    {
        $this->beginTransaction();
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return;
        }

        $query_sql = sprintf("delete from columns where %s = :value", $field);
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );
        $this->commit();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return int
     */
    public function count(string $field, mixed $value): int
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return 0;
        }

        $query_sql = sprintf("select count(*) from columns where %s = :value", $field);
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );

        return $stmt->fetchColumn() ?: 0;
    }
}
