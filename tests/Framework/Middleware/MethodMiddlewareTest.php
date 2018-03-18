<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\MethodMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class MethodMiddlewareTest extends TestCase
{

    /**
     * @var MethodMiddleware
     */
    private $middleware;

    public function setUp()
    {
        $this->middleware = new MethodMiddleware();
    }

    public function testAddMethod()
    {
        $request = (new ServerRequest('POST', '/demo'))
            ->withParsedBody(['_method' => 'DELETE']);

        call_user_func_array($this->middleware, [$request, function(ServerRequestInterface $request) {
            $this->assertEquals('DELETE', $request->getMethod());
        }]);

    }
}