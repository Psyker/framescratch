<?php

namespace App\Blog\Actions;

use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryShowAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var PostRepository
     */
    private $postRepository;


    public function __construct(
        RendererInterface $renderer,
        PostRepository $postRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->renderer = $renderer;
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $category = $this->categoryRepository->findBy('slug', $request->getAttribute('slug'));
        $posts = $this->postRepository->findPaginatedForCategory(15, $params['p'] ?? 1, $category->id);
        $categories = $this->categoryRepository->findAll();

        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'category'));
    }
}
