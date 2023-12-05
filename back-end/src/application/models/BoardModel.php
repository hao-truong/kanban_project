<?php
declare(strict_types=1);

namespace app\models;

use app\core\Model;
use PDO;
use PDOException;
use shared\enums\StatusCode;
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
     * @throws ResponseException
     */
    public function save(array $entity): array
    {
        $query_sql = "insert into boards (title, creator_id) values (:title, :creator_id)";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                $entity
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
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
        if (!in_array($field, $this->ALLOW_FIELD)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        $query_sql = "select * from boards where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "value" => $value,
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
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
        $query_sql = "UPDATE boards SET title = :title, created_at = :created_at, updated_at = :updated_at, creator_id = :creator_id WHERE id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute($entity);
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

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
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        $query_sql = "select * from boards where " . $field . " = :value";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "value" => $value,
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
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
        $query_sql = "delete from boards where id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "id" => $id,
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }
    }
}
