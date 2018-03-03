<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{

    protected $viewPath = "@blog/admin/posts";

    protected $routePrefix = "admin.blog";

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        RendererInterface $renderer,
        PostRepository $repository,
        Router $router,
        FlashService $flash,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($renderer, $repository, $router, $flash);
        $this->categoryRepository = $categoryRepository;
    }

    protected function getParams(Request $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function getValidator(Request $request)
    {
        return (parent::getValidator($request))
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->exists('category_id', $this->categoryRepository->getTable(), $this->categoryRepository->getPdo())
            ->dateTime('created_at')
            ->slug('slug');
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryRepository->findList();

        return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime();

        return $post;
    }
}
