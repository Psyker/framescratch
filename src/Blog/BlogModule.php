<?php

namespace App\Blog;

use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule
{
    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z\-]+}', [$this, 'show'], 'blog.show');
    }

    public function index(): string
    {
        return '<h1>Blog</h1>';
    }

    public function show(Request $request): string
    {
        return "<h1>Post {$request->getAttribute('slug')}</h1>";
    }
}
