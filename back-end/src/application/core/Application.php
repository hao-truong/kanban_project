<?php

declare(strict_types=1);

namespace app\core;

use shared\exceptions\ResponseException;
use shared\handlers\MigrationHandler;

class Application
{
    public function __construct(
        private readonly Response $response,
        private readonly Request $request,
        public readonly Router $router,
        private readonly Database $database,
        private readonly MigrationHandler $migrationHandler
    ) {
    }

    public function run(): void
    {
        try {
            // run migration
            $this->migrationHandler->runMigration("/upgrades/board_upgrade_table.sql");
            $this->migrationHandler->runMigration("/upgrades/user_upgrade_table.sql");
            $this->migrationHandler->runMigration("/upgrades/user_board_upgrade_table.sql");
            $this->migrationHandler->runMigration("/upgrades/column_upgrade_table.sql");
            $this->migrationHandler->runMigration("/upgrades/card_upgrade_table.sql");

            echo $this->router->resolve($this->request, $this->response);
        } catch (ResponseException $exception) {
            echo $this->response->content(
                $exception->getStatusCode(),
                $exception->getMessage(),
                $exception->getErrors(),
                null
            );
        }
    }
}
