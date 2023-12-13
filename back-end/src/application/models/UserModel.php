<?php
declare(strict_types=1);

namespace app\models;

use app\core\Model;
use app\entities\UserEntity;
use app\models\IModel;
use PDO;
use PDOException;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class UserModel extends Model implements IModel
{
    private array $ALLOW_FIELD = [
        'username',
        'id',
        'email',
        'access_token',
        'refresh_token',
    ];

    /**
     * @param $entity array ["username" => string, "password" => string, "alias" => string]
     * @return array
     */
    public function save(array $entity): array
    {
        $query_sql = "insert into users (username, password, alias, email) values (:username, :password, :alias, :email)";
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
        if (!in_array($field, $this->ALLOW_FIELD)) {
            error_log("Field is not allowed");
            return null;
        }

        $query_sql = "select * from users where " . $field . " = :value";
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
        $query_sql = "UPDATE users SET username = :username, password = :password, alias = :alias, email = :email, access_token = :access_token, refresh_token = :refresh_token WHERE id = :id";
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
        if (!in_array($field, $this->ALLOW_FIELD)) {
            error_log("Field is not allowed");
            return [];
        }

        $query_sql = "select * from users where " . $field . " = :value";
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
     * @param int $id
     * @return void
     */
    public function deleteById(mixed $id): void
    {
        $query_sql = "delete from users where id = :user_id";
        $stmt = $this->database->getConnection()->prepare($query_sql);
        $stmt->execute(
            [
                "user_id" => $id,
            ]
        );
    }
}
