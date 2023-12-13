<?php
declare(strict_types=1);

namespace app\core;

use shared\enums\RequestMethod;

class Request
{
    private array $params = [];
    private array $queries = [];
    private int | null $userId = null;

    public function __construct() { }

    /**
     * @return RequestMethod
     */
    public function getMethod(): RequestMethod
    {
        return RequestMethod::from(strtoupper($_SERVER['REQUEST_METHOD']));
    }

    /**
     * @return string
     */
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

    /**
     * @param string $path_component
     * @return void
     */
    public function extractQueries(string $path_component): void
    {
        $query_component_list = explode('&', $path_component);
        foreach ($query_component_list as $query_component) {
            $pair_query = explode('=', $query_component);
            $this->queries["$pair_query[0]"] = $pair_query[1];
        }
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        $body = [];

        switch ($this->getMethod()) {
            case RequestMethod::POST:
            case RequestMethod::PUT:
            case RequestMethod::PATCH:
                $body = json_decode(file_get_contents('php://input'), true);
                break;
            default:
                break;
        }

        return $body;
    }


    /**
     * @param string $param_name
     * @param string $param_value
     * @return void
     */
    public function setParam(string $param_name, string $param_value): void
    {
        $this->params[$param_name] = $param_value;
    }

    /**
     * @param string $param_name
     * @return string
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

    /**
     * @param string $param_name
     * @return int
     * @throws \Exception
     */
    public function getIntParam(string $param_name): int
    {
        if (!array_key_exists($param_name, $this->params)) {
            error_log("Param {$param_name} does not exist.");
            throw new \Exception("Param {$param_name} does not exist.");
        }

        return intval($this->params[$param_name]);
    }

    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    public function setUserId(?int $user_id): void {
        $this->userId = $user_id;
    }

    public function getUserId(): int {
        return $this->userId;
    }
}
