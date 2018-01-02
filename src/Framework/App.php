<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{
    /**
     * List of modules.
     * @var array
     */
    private $modules = [];

    /**
     * @var ContainerInterface;
     */
    private $container;

    /**
     * App constructor.
     *
     * @param ContainerInterface $container
     * @param string[] $modules Modules list to load.
     * @internal param array $dependencies
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }
    }
    /**
     * Run the app.
     *
     * @param ServerRequestInterface $request
     *
     * @return Response
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request): Response
    {
        // If the uri contain a / at his end, redirect to the "without /" version of the uri.
        $uri = $request->getUri()->getPath();
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_method', $parsedBody) &&
            in_array($parsedBody['_method'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        $route = $this->container->get(Router::class)->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Error 404</h1>');
        }
        foreach ($route->getParams() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
