<?php

namespace Framework\Router;

/**
 * Class Route
 * Represent a matched route.
 * @package Framework\Router
 */
class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(string $name, callable $callable, array $parameters)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callable;
    }

    /**
     * Retrieve URL's parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
