<?php
declare(strict_types=1);

namespace shared\handlers;

use app\core\Database;

class MigrationHandler
{
    public function __construct(private readonly Database $database)
    {
    }

    public function runMigration(string $file_name): void
    {
        $sql = file_get_contents(dirname(__DIR__) . "/migrations/" . $file_name);
        $this->database->getConnection()->exec($sql);
    }
}
