<?php
declare(strict_types=1);

namespace shared\utils;

use app\entities\UserEntity;

class ArrayToEntityConverter {

    public static function ToUserEntity(array $data): UserEntity {
        return new UserEntity($data);
    }
}
