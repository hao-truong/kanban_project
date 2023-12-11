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
     * @throws ResponseException
     */
    public function save(array $entity): array
    {
        $query_sql = "insert into cards (title, column_id, position) values (:title, :column_id, :position)";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                $entity
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        $last_insert_id = $this->database->getConnection()->lastInsertId();

        return $this->findOne('id', $last_insert_id);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @return array|null
     * @throws ResponseException
     */
    public function findOne(mixed $field, mixed $value): array|null
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        $query_sql = "select * from cards where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "value" => $value,
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * @param array $entity
     * @return array
     * @throws ResponseException
     */
    public function update(array $entity): array
    {
        $query_sql = "UPDATE cards SET title = :title, created_at = :created_at, updated_at = :updated_at, assigned_user = :assigned_user, ";
        $query_sql .= "column_id = :column_id, description = :description, status = :status, position = :position WHERE id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute($entity);
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        return $this->findOne('id', $entity['id']);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return array
     * @throws ResponseException
     */
    public function find(string $field, mixed $value): array
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        $query_sql = "select * from cards where " . $field . " = :value order by position ASC";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "value" => $value,
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result ?: [];
    }

    /**
     * @param mixed $id
     * @return void
     * @throws ResponseException
     */
    public function deleteById(mixed $id): void
    {
        $query_sql = "delete from cards where id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "id" => $id,
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return int
     * @throws ResponseException
     */
    public function count(string $field, mixed $value): int
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        $query_sql = "select count(*) from cards where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "value" => $value,
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return void
     * @throws ResponseException
     */
    public function delete(string $field, mixed $value): void
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        $query_sql = "delete from cards where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "value" => $value,
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param array $tables_to_join
     * @param array $condition
     * @return array
     * @throws ResponseException
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

        try {
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, ErrorMessage::INTERNAL_SERVER_ERROR);
        }

        return $result;
    }
}
