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

            echo $this->router->resolve($this->request, $this->response);
        } catch (ResponseException $exception) {
            echo $this->response->content($exception->getStatusCode(), $exception->getMessage(), $exception->getErrors(), null);
        }
    }
}
