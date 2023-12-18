<?php

declare(strict_types=1);

namespace app\core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use shared\enums\RequestMethod;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;
use shared\handlers\MatchedRouteHandler;
use shared\types\RouteElement;

class Router
{
    public Request $request;
    private array $routes = [];
    private string $prefixEndpoint = "/api/v1";
    const PARAM_PATTERN = "~^\{(\w+)\}$~";
    const SUBSTITUTION_PARAM_PATTERN = "([\w-]+)";

    public function __construct(private readonly ContainerInterface $container)
    {
        foreach (RequestMethod::cases() as $method) {
            $this->routes[$method->name] = [];
        }
    }

    /**
     * @param RequestMethod $method
     * @param string $endpoint
     * @param []string $middlewares
     * @param array $call_backs
     * @return void
     * @throws \Exception
     * @throws ContainerExceptionInterface
     */
    public function addRoute(RequestMethod $method, string $endpoint, ?array $middlewares, array $call_backs): void
    {
        $route_element = $this->handleRoute($this->prefixEndpoint . $endpoint);
        $this->routes[$method->name][$route_element->getEndpoint()]['call_back'] = $call_backs;

        if ($middlewares) {
            $this->routes[$method->name][$route_element->getEndpoint()]['middlewares'] = [];
            foreach ($middlewares as $index => $middleware_name) {
                if (!class_exists($middleware_name)) {
                    throw new \Exception("{$middleware_name} do not exist!");
                }

                $this->routes[$method->name][$route_element->getEndpoint()]['middlewares'][] = $this->container->get(
                    $middleware_name
                );
            }
        }

        if (count($route_element->getParams()) === 0) {
            return;
        }

        $this->routes[$method->name][$route_element->getEndpoint()]['param_key_list'] = $route_element->getParams();
        foreach ($route_element->getParams() as $key => $param_key) {
            $this->routes[$method->name][$route_element->getEndpoint()][$param_key] = null;
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return void
     * @throws ResponseException
     */
    public function resolve(Request $request, Response $response): void
    {
        $this->request = $request;
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        $result_matched_handler = $this->matchedRoute($method, $path) ?? false;

        if (!$result_matched_handler) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, "Page not found!");
        }

        $result_matched_handler->run($this->container);
    }

    /**
     * @param RequestMethod $method
     * @param string $endpoint_to_check
     * @return MatchedRouteHandler|null
     */
    private function matchedRoute(RequestMethod $method, string $endpoint_to_check): MatchedRouteHandler|null
    {
        foreach ($this->routes[$method->name] as $endpoint_key => $endpoint_pattern) {
            if (preg_match($endpoint_key, $endpoint_to_check, $param_values)) {
                if (array_key_exists('param_key_list', $this->routes[$method->name][$endpoint_key])) {
                    $param_key_list = $this->routes[$method->name][$endpoint_key]['param_key_list'];

                    foreach ($param_key_list as $index => $param_key) {
                        $this->request->setParam($param_key, $param_values[$index + 1]);
                    }
                }

                $callback = $this->routes[$method->name][$endpoint_key]['call_back'];
                $middlewares = array_key_exists(
                    'middlewares',
                    $this->routes[$method->name][$endpoint_key]
                ) ? $this->routes[$method->name][$endpoint_key]['middlewares'] : [];

                return new MatchedRouteHandler($callback, $middlewares);
            }
        }

        return null;
    }

    /**
     * @param string $endpoint
     * @return RouteElement
     */
    private function handleRoute(string $endpoint): RouteElement
    {
        $path_component_list = explode('/', $endpoint);

        $param_key_list = [];
        foreach ($path_component_list as $index => $path_component) {
            if (preg_match(self::PARAM_PATTERN, $path_component, $matched_param_list)) {
                $path_component_list[$index] = self::SUBSTITUTION_PARAM_PATTERN;
                $param_key_list[] = $matched_param_list[1];
            }
        }

        if (count($param_key_list) === 0) {
            $refactor_endpoint = '~^' . $endpoint . '$~';
            return new RouteElement($refactor_endpoint, $param_key_list);
        }

        $refactor_endpoint = '~^' . implode('/', $path_component_list) . '$~';
        return new RouteElement($refactor_endpoint, $param_key_list);
    }
}
