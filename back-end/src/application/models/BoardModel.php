<?php

declare(strict_types=1);

namespace app\models;

use app\core\Model;
use PDO;
use shared\exceptions\ResponseException;

class  BoardModel extends Model implements IModel
{
    private array $ALLOW_FIELD = [
        'id',
        'creator_id',
        'title',
    ];

    /**
     * @param array $entity
     * @return array
     */
    public function save(array $entity): array
    {
        $this->beginTransaction();

        $query_sql = "insert into boards (title, creator_id) values (:title, :creator_id)";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            $entity
        );

        $last_insert_id = $this->database->getConnection()->lastInsertId();
        $this->commit();
        return $this->findOne('id', $last_insert_id);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @return array|null
     */
    public function findOne(mixed $field, mixed $value): array|null
    {
        if (!in_array($field, $this->ALLOW_FIELD)) {
            error_log("Field is not allowed");
            return null;
        }

        $query_sql = sprintf("select * from boards where %s = :value", $field);
        $query_sql = addslashes($query_sql);
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
        $query_sql = "UPDATE boards SET title = :title, created_at = :created_at, updated_at = :updated_at, creator_id = :creator_id WHERE id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute($entity);
        $this->commit();
        return $this->findOne('id', strval($entity['id']));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return array
     * @throws ResponseException
     */
    public function find(string $field, mixed $value): array
    {
        if (!in_array($field, $this->ALLOW_FIELD)) {
            error_log("Field is not allowed");
            return [];
        }

        $query_sql = sprintf("select * from boards where %s = :value", $field);
        $query_sql = addslashes($query_sql);
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result ?: [];
    }

    /**
     * @param mixed $id
     * @return void
     */
    public function deleteById(mixed $id): void
    {
        $this->beginTransaction();
        $query_sql = "delete from boards where id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "id" => $id,
            ]
        );
        $this->commit();
    }

    public function search(string $field, string $search_value, int $user_id): array
    {
        if (!in_array($field, $this->ALLOW_FIELD)) {
            error_log("Field is not allowed");
            return [];
        }

        $query_sql = "select * from boards as b  left join user_board as ub on b.id = ub.board_id";
        $query_sql .= sprintf(" where %s like :search_value and ub.user_id = :user_id", $field);
        $query_sql = addslashes($query_sql);
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "search_value" => "%" . $search_value . "%",
                "user_id" => $user_id,
            ]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
