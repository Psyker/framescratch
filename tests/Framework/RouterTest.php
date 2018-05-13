<?php

namespace Tests\Framework;

use Fig\Http\Message\RequestMethodInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    /**
     * @var Router
     */
    private $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest(RequestMethodInterface::METHOD_GET, '/blog');
        $this->router->get('/blog', function () { return 'Hello'; }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('Hello', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIfURLDoesNotExists()
    {
        $request = new ServerRequest(RequestMethodInterface::METHOD_GET, '/blog');
        $this->router->get('/blogeuh', function () { return 'Hello'; }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParams()
    {
        $request = new ServerRequest(RequestMethodInterface::METHOD_GET, '/blog/slug');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}', function () { return 'Hello'; }, 'post.show');
        $this->router->get('/blog', function () { return 'Bonsoir'; }, 'posts');
        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('Hello', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'slug'], $route->getParams());
        // Test invalid URL
        $route = $this->router->match(new ServerRequest('GET', '/blog/premier_post/1'));
        $this->assertEquals(null, $route);
    }

    public function testGenerateUri()
    {
        $this->router->get('/blog/{slug:[a-z0-9\-]+}', function () { return 'Hello'; }, 'post.show');
        $this->router->get('/blog', function () { return 'Bonsoir'; }, 'posts');
        $uri = $this->router->generateUri('post.show', ['slug' => 'post']);
        $this->assertEquals('/blog/post', $uri);
    }

    public function testGenerateUriWithQueryParams()
    {
        $this->router->get('/blog/{slug:[a-z0-9\-]+}', function () { return 'Hello'; }, 'post.show');
        $this->router->get('/blog', function () { return 'Bonsoir'; }, 'posts');
        $uri = $this->router->generateUri(
            'post.show',
            ['slug' => 'post'],
            ['p' => 2]
        );
        $this->assertEquals('/blog/post?p=2', $uri);
    }
}