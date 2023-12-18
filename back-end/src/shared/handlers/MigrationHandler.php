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
        $file_path = sprintf("%s/migrations%s", dirname(__DIR__), $file_name);

        if (file_exists($file_path)) {
            $sql = file_get_contents($file_path);
            $this->database->getConnection()->exec($sql);
        } else {
            echo "File not found: $file_path";
        }
    }
}
