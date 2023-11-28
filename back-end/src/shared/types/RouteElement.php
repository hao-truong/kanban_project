<?php
declare(strict_types=1);

namespace shared\types;

class RouteElement
{
    private string $endpoint;
    private array $params;

    /**
     * @param string $endpoint
     * @param array $params
     */
    public function __construct(string $endpoint, array $params)
    {
        $this->endpoint = $endpoint;
        $this->params = $params;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
