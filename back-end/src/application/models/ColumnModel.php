<?php
declare(strict_types=1);

namespace app\models;

use app\core\Model;
use app\entities\ColumnEntity;
use PDO;
use PDOException;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class ColumnModel extends Model implements IModel
{
    private array $ALLOW_FIELDS = [
        'id',
        'creator_id',
        'board_id',
    ];

    public function save(array $entity): array
    {
        $query_sql = "insert into columns (title, creator_id, board_id, position) values (:title, :creator_id, :board_id, :position)";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            $entity
        );

        $last_insert_id = $this->database->getConnection()->lastInsertId();
        return $this->findOne('id', $last_insert_id);
    }

    public function findOne(mixed $field, mixed $value): array|null
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return null;
        }

        $query_sql = "select * from columns where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function update(array $entity): array
    {
        $query_sql = "UPDATE columns SET title = :title, created_at = :created_at, updated_at = :updated_at, creator_id = :creator_id, board_id = :board_id, position = :position WHERE id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute($entity);
        return $this->findOne('id', strval($entity['id']));
    }

    public function find(string $field, mixed $value): array
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return [];
        }

        $query_sql = "select * from columns where " . $field . " = :value order by position ASC";
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
        $query_sql = "delete from columns where id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "id" => $id,
            ]
        );
    }

    public function delete(string $field, mixed $value): void
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return;
        }

        $query_sql = "delete from columns where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );
    }

    public function count(string $field, mixed $value): int
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return 0;
        }

        $query_sql = "select count(*) from columns where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );

        return $stmt->fetchColumn() ?: 0;
    }
}
