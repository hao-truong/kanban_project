<?php
declare(strict_types=1);

namespace app\core;

abstract class Model
{
    public function __construct(protected readonly Database $database)
    {
    }
}
