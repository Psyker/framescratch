<?php

namespace App\Blog;

use Framework\Renderer;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule
{
    /**
     * @var Renderer
     */
    private $renderer;

    public function __construct(Router $router, Renderer $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z\-]+}', [$this, 'show'], 'blog.show');
    }

    public function index(): string
    {
        return $this->renderer->render('@blog/index');
    }

    public function show(Request $request): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }
}
