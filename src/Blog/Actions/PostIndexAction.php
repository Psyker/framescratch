<?php

namespace App\Blog\Actions;

use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostIndexAction
{

    use RouterAwareAction;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostRepository
     */
    private $repository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * PostIndexAction constructor.
     * @param RendererInterface $renderer
     * @param PostRepository $repository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        RendererInterface $renderer,
        PostRepository $repository,
        CategoryRepository $categoryRepository
    ) {
        $this->renderer = $renderer;
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(Request $request)
    {
        $params = $request->getQueryParams();
        $posts = $this->repository->findPaginatedPublic(15, $params['p'] ?? 1);
        $categories = $this->categoryRepository->findAll();

        return $this->renderer->render('@blog/index', compact('posts', 'categories'));
    }
}
