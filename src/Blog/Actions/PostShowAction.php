<?php

namespace App\Blog\Actions;

use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostShowAction
{

    use RouterAwareAction;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var PostRepository
     */
    private $repository;


    public function __construct(
        RendererInterface $renderer,
        PostRepository $repository,
        Router $router
    ) {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->repository = $repository;
    }

    public function __invoke(Request $request)
    {
        $slug = $request->getAttribute('slug');
        $post = $this->repository->findWithCategory($request->getAttribute('id'));

        if ($post->slug !== $slug) {
            return   $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }

        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}
