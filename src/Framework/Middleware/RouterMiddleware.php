<?php

namespace Framework\Middleware;

use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddleware
{

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {

        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $this->router->match($request);
        if (is_null($route)) {
            return $next($request);
        }
        foreach ($route->getParams() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        $request = $request->withAttribute(get_class($route), $route);

        return $next($request);
    }
}
