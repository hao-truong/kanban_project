<?php

namespace app\core;

use PDO;

class Database
{
    private PDO $connection;

    public function __construct()
    {
        $database_type = $_ENV['DB_TYPE'] ?? "";
        $database_host = $_ENV['DB_HOST'] ?? "";
        $database_name = $_ENV['DB_NAME'] ?? "";
        $username = $_ENV['DB_USER'] ?? "";
        $password = $_ENV['DB_PASS'] ?? "";
        $port = $_ENV['DB_PORT'] ?? "";

        if ($database_type == "" || $database_host == "" || $username == "" || $password == "" || $database_name == "") {
            throw new \Exception("Database cannot be connected due to wrong configuration!");
        }

        $this->connection = new PDO("$database_type:host=$database_host;port=$port;dbname=$database_name", $username, $password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function runMigration(string $file_name) {
        $sql = file_get_contents( dirname(__DIR__)."/../shared/migrations/".$file_name);
        $this->connection->exec($sql);
    }

    public function getConnection() {
        return $this->connection;
    }
}
