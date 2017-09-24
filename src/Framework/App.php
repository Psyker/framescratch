<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
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
     * @var Router;
     */
    private $router;

    /**
     * App constructor.
     *
     * @param string[] $modules Modules list to load.
     * @param array $dependencies
     */
    public function __construct(array $modules = [], array $dependencies = [])
    {
        $this->router = new Router();
        if (array_key_exists('renderer', $dependencies)) {
            $dependencies['renderer']->addGlobal('router', $this->router);
        }
        foreach ($modules as $module) {
            $this->modules[] = new $module($this->router, $dependencies['renderer']);
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
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        $route = $this->router->match($request);
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }
        foreach ($route->getParams() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        $response = call_user_func_array($route->getCallback(), [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }
}
