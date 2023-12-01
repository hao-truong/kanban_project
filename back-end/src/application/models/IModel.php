<?php
declare(strict_types=1);

namespace app\models;

interface IModel {
    public function save(array $entity): array;
    public function findOne(string $field, string $value): array | null;
    public function update(array $entity): array;
    public function find(string $field, mixed $value): array;
}
