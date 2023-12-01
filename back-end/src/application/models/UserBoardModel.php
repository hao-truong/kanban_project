<?php
declare(strict_types=1);

namespace app\models;

use app\core\Model;
use app\entities\ColumnEntity;
use app\entities\UserBoardEntity;
use PDO;
use PDOException;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class UserBoardModel extends Model implements IModel
{

    private array $ALLOW_FIELDS = [
        'user_id',
        'board_id'
    ];

    public function save(array $entity): array
    {
        new UserBoardEntity(
            $entity['userId'], $entity['boardId']
        );

        $query_sql = "insert into user_board (user_id, board_id) values (:userId, :boardId)";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                $entity
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        return $this->findOne(
            [
                'user_id',
                'board_id'
            ], [
                'user_id'  => $entity['userId'],
                'board_id' => $entity['boardId'],
            ]
        );
    }

    public function findOne(mixed $field, mixed $value): array|null
    {
        $query_sql = "select * from user_board where user_id = :user_id and board_id = :board_id";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                $value,
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function update(array $entity): array
    {
        return [];
    }

    public function find(string $field, mixed $value): array
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        $query_sql = "select * from user_board where " . $field . " = :value";
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

    public function deleteById(mixed $id): void
    {
        $query_sql = "delete from user_board where user_id = :user_id and board_id = :board_id";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                [
                    "user_id"  => $id[0],
                    "board_id" => $id[1],
                ]
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }
    }

    public function delete(string $field, mixed $value): void
    {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        $query_sql = "delete from user_board where " . $field . " = :value";
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
    }

    public function join(array $tables_to_join, array $condition)
    {
        $query_sql = "select ";
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
        $query_sql .= "from user_board as ub \n";
        $query_sql .= "join {$tables_to_join['table']} as {$tables_to_join['as']} on ub.{$tables_to_join['condition'][0]} = {$tables_to_join['as']}.{$tables_to_join['condition'][1]} \n";
        $query_sql .= "where ub.{$condition['where'][0]} = {$condition['where'][1]}";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        return $result;
    }

    public function count(string $field, mixed $value): int {
        if (!in_array($field, $this->ALLOW_FIELDS)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        $query_sql = "select count(*) from user_board where ".$field." = :value";
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

        return $stmt->fetchColumn();
    }
}
