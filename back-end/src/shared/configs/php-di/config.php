<?php
declare(strict_types=1);

use DI\ContainerBuilder;

$container_builder = new ContainerBuilder;
try {
    $container = $container_builder->build();

    return $container;
} catch (Exception $e) {
    error_log($e->getMessage());
}
