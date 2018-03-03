<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminBlogAction
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

    /**
     * @var FlashService
     */
    private $flash;

    public function __construct(
        RendererInterface $renderer,
        PostRepository $repository,
        Router $router,
        SessionInterface $session,
        FlashService $flash
    ) {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->repository = $repository;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->repository->findPaginated(15, $params['p'] ?? 1);

        return $this->renderer->render('@blog/admin/index', compact('items'));
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
        $post = $this->repository->find($request->getAttribute('id'));

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

    /**
     * Edit post
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->repository->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i:s');
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->repository->update($item->id, $params);
                $this->flash->addFlash('success', 'The post has been edited well.');

                return $this->redirect('admin.blog.index');
            }
            $errors = $validator->getErrors();
            $item->content = $params['content'];
            $item->name = $params['name'];
            $item->slug = $params['slug'];
        }

        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
    }

    /**
     * Create post
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $params = [];
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->repository->insert($params);
                $this->flash->addFlash('success', "The article has been well created");
                return $this->redirect('admin.blog.index');
            }
            $errors = $validator->getErrors();
        }
        $item = new Post();
        $item->created_at = new \DateTime();

        if (!empty($params)) {
            $item->content = $params['content'];
            $item->slug = $params['slug'];
            $item->name = $params['name'];
        }

        return $this->renderer->render('@blog/admin/create', compact('item', 'errors'));
    }

    public function delete(Request $request)
    {
        $this->repository->delete($request->getAttribute('id'));
        return $this->redirect('admin.blog.index');
    }

    private function getParams(Request $request)
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getValidator(Request $request)
    {
        return (new Validator($request->getParsedBody()))
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('name', 2, 50)
            ->dateTime('created_at')
            ->slug('slug');
    }
}
