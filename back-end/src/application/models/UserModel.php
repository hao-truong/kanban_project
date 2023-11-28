<?php
declare(strict_types=1);

namespace app\models;

use app\core\Model;
use app\entities\UserEntity;
use app\models\IModel;
use PDO;
use PDOException;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class UserModel extends Model implements IModel
{
    private array $ALLOW_FIELD = [
        'username',
        'id',
        'email',
        'access_token',
        'refresh_token'
    ];

    /**
     * @param $entity array ["username" => string, "password" => string, "alias" => string]
     * @return false|string
     * @throws ResponseException
     */
    public function save(array $entity): array
    {
        $user_entity = new UserEntity(
            $entity["username"], $entity["password"], $entity["email"], $entity["alias"]
        );

        $query_sql = "insert into users (username, password, alias, email) values (:username, :password, :alias, :email)";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute(
                $user_entity->toArray()
            );
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        $last_insert_id = $this->database->getConnection()->lastInsertId();

        return $this->findOne('id', $last_insert_id);
    }

    public function findOne(string $field, string $value): array|null
    {
        if (!in_array($field, $this->ALLOW_FIELD)) {
            error_log("Field is not allowed");
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
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

    public function update(array $entity): array
    {
        $query_sql = "UPDATE users SET username = :username, password = :password, alias = :alias, email = :email, access_token = :access_token, refresh_token = :refresh_token WHERE id = :id";
        $stmt = $this->database->getConnection()->prepare($query_sql);

        try {
            $stmt->execute($entity);
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new ResponseException(StatusCode::INTERNAL_SERVER_ERROR, StatusCode::INTERNAL_SERVER_ERROR->name, "Internal server error");
        }

        return $this->findOne('id', strval($entity['id']));
    }
}
