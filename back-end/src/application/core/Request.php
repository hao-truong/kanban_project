<?php
declare(strict_types=1);

namespace app\core;

use shared\enums\RequestMethod;

class Request
{
    private array $params = [];
    private array $queries = [];

    public function __construct() { }

    public function getMethod(): RequestMethod
    {
        return RequestMethod::from(strtoupper($_SERVER['REQUEST_METHOD']));
    }

    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?') ?? false;

        if ($position === false) {
            return $path;
        }

        $this->extractQueries(substr($path, $position + 1));
        return substr($path, 0, $position);
    }

    public function extractQueries(string $path_component): void
    {
        $query_component_list = explode('&', $path_component);
        foreach ($query_component_list as $query_component) {
            $pair_query = explode('=', $query_component);
            $this->queries[] = $pair_query;
        }
    }

    public function getBody(): array
    {
        $body = [];

        switch ($this->getMethod()) {
            case RequestMethod::POST:
                $body = json_decode(file_get_contents('php://input'), true);
                break;
            default:
                break;
        }

        return $body;
    }

    public function setParam(string $param_name, string $param_value): void
    {
        $this->params[$param_name] = $param_value;
    }

    /**
     * @throws \Exception
     */
    public function getParam(string $param_name): string
    {
        if (!array_key_exists($param_name, $this->params)) {
            error_log("Param {$param_name} does not exist.");
            throw new \Exception("Param {$param_name} does not exist.");
        }

        return $this->params[$param_name];
    }

    public function getQueries(): array
    {
        return $this->queries;
    }
}
