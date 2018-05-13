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

    /**
     * Route constructor.
     * @param string $name
     * @param string|callable $callable
     * @param array $parameters
     */
    public function __construct(string $name, $callable, array $parameters)
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
     * @return string|callable
     */
    public function getCallback()
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
