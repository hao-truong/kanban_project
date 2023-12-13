<?php
declare(strict_types=1);

namespace app\models;

use app\core\Model;
use PDO;
use PDOException;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class CardModel extends Model implements IModel
{
    private array $ALLOW_FIELDS = [
        'id',
        'column_id'
    ];

    /**
     * @param array $entity
     * @return array
     */
    public function save(array $entity): array
    {
        $query_sql = "insert into cards (title, column_id, position) values (:title, :column_id, :position)";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            $entity
        );

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

        $query_sql = "select * from cards where " . $field . " = :value";
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
        $query_sql = "UPDATE cards SET title = :title, created_at = :created_at, updated_at = :updated_at, assigned_user = :assigned_user, ";
        $query_sql .= "column_id = :column_id, description = :description, status = :status, position = :position WHERE id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute($entity);

        return $this->findOne('id', $entity['id']);
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

        $query_sql = "select * from cards where " . $field . " = :value order by position ASC";
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
        $query_sql = "delete from cards where id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "id" => $id,
            ]
        );
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

        $query_sql = "select count(*) from cards where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );

        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function delete(string $field, mixed $value): void
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            return;
        }

        $query_sql = "delete from cards where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );
    }

    /**
     * @param array $tables_to_join
     * @param array $condition
     * @return array
     */
    public function join(array $tables_to_join, array $condition): array
    {
        $query_sql = "select c.*, ";
        $fields = $tables_to_join['select'];

        if (!empty($fields)) {
            $query_sql .= implode(
                ", ", array_map(
                        function ($field) use ($tables_to_join) {
                            return "{$tables_to_join['as']}.$field";
                        }, $fields
                    )
            );
        }

        $query_sql .= "\n";
        $query_sql .= "from cards as c \n";
        $query_sql .= "left join {$tables_to_join['table']} as {$tables_to_join['as']} on c.{$tables_to_join['condition'][0]} = {$tables_to_join['as']}.{$tables_to_join['condition'][1]} \n";

        if (count($condition) !== 0) {
            $query_sql .= "where c.{$condition['where'][0]} = {$condition['where'][1]}";
        }

        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
