<?php

namespace App\Blog\Actions;

use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{

    use RouterAwareAction;

    /**
     * @var RendererInterface
     */
    private $renderer;
    private $pdo;
    private $router;

    public function __construct(RendererInterface $renderer, \PDO $pdo, Router $router)
    {
        $this->router = $router;
        $this->pdo = $pdo;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request)
    {
        if ($request->getAttribute('id')) {
            return $this->show($request);
        }
        return $this->index();
    }

    public function index(): string
    {
        $posts = $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')
            ->fetchAll();
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    /**
     * Display an article
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function show(Request $request)
    {
        $slug = $request->getAttribute('slug');
        $query = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$request->getAttribute('id')]);
        $post = $query->fetch(\PDO::FETCH_OBJ);

        if ($post->slug !== $slug) {
            return  $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }

        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}
