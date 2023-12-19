<?php

declare(strict_types=1);

namespace app\core;

abstract class Model
{
    public function __construct(protected readonly Database $database)
    {
    }

    public function beginTransaction()
    {
        $this->database->getConnection()->beginTransaction();
    }

    public function commit()
    {
        $this->database->getConnection()->commit();
    }

    public function rollback()
    {
        $this->database->getConnection()->rollback();
    }
}
