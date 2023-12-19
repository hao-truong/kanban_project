<?php

declare(strict_types=1);

namespace app\models;

use app\core\Model;
use PDO;

class UserBoardModel extends Model implements IModel
{

    private array $ALLOW_FIELDS = [
        'user_id',
        'board_id'
    ];

    public function save(array $entity): array
    {
        $this->beginTransaction();
        $query_sql = "insert into user_board (user_id, board_id) values (:user_id, :board_id)";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            $entity
        );
        $this->commit();

        return $this->findOne(
            null, [
                'user_id' => $entity['user_id'],
                'board_id' => $entity['board_id'],
            ]
        );
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @return array|null
     */
    public function findOne(mixed $field, mixed $value): array|null
    {
        $query_sql = "select * from user_board where user_id = :user_id and board_id = :board_id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            $value
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function update(array $entity): array
    {
        return [];
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

        $query_sql = sprintf("select * from user_board where %s = :value", $field);
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
        $query_sql = "delete from user_board where user_id = :user_id and board_id = :board_id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "user_id" => $id[0],
                "board_id" => $id[1],
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

        $query_sql = sprintf("delete from user_board where %s = :value", $field);
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );
        $this->commit();
    }

    /**
     * @param array $tables_to_join
     * @param array $condition
     * @return array
     */
    public function join(array $tables_to_join, array $condition): array
    {
        $query_sql = "select ";
        $fields = $tables_to_join['select'];

        if (!empty($fields)) {
            $query_sql .= implode(
                ", ",
                array_map(
                    function ($field) use ($tables_to_join) {
                        return "{$tables_to_join['as']}.$field";
                    },
                    $fields
                )
            );
        }
        $query_sql .= "\n";
        $query_sql .= "from user_board as ub \n";
        $query_sql .= "join {$tables_to_join['table']} as {$tables_to_join['as']} on ub.{$tables_to_join['condition'][0]} = {$tables_to_join['as']}.{$tables_to_join['condition'][1]} \n";
        $query_sql .= "where ub.{$condition['where'][0]} = {$condition['where'][1]}";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        $query_sql = sprintf("select count(*) from user_board where %s = :value", $field);
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "value" => $value,
            ]
        );

        return $stmt->fetchColumn();
    }
}
