<?php
declare(strict_types=1);

namespace shared\handlers;

use Psr\Container\ContainerInterface;

class MatchedRouteHandler
{
    private array $callback;
    private array $middlewares;

    /**
     * @param array $callback
     * @param []IMiddleware $middlewares
     */
    public function __construct(array $callback, array $middlewares)
    {
        $this->callback = $callback;
        $this->middlewares = $middlewares;
    }

    public function run(ContainerInterface $container): mixed
    {
        foreach ($this->middlewares as $middleware) {
            $middleware->execute();
        }

        return $container->call($this->callback);
    }
}
