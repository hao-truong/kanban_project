<?php
declare(strict_types=1);

namespace app\core;

use shared\exceptions\ResponseException;

class Application
{
    public function __construct(
        public Response $response,
        public Request $request,
        public Router $router,
        public Database $database)
    {

    }

    public function run(): void
    {
        try {
            // run migration
            $this->database->runMigration("migration_user_create_table_1.sql");
            $this->database->runMigration("board_create_table.sql");
            $this->database->runMigration("user_board_create_table.sql");
            $this->database->runMigration("column_create_table.sql");
            $this->database->runMigration("card_create_table.sql");

            echo $this->router->resolve($this->request, $this->response);
        } catch (ResponseException $exception) {
            echo $this->response->content($exception->getStatusCode(), $exception->getMessage(), $exception->getErrors(), null);
        }
    }
}
