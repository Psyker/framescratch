<?php

namespace Framework\Actions;

use App\Framework\Database\Repository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
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
     * @var Repository
     */
    private $repository;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var string
     */
    protected $viewPath = null;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var array
     */
    protected $messages = [
        'create' => "The element has been well created.",
        'edit' => "The element has been well edited"
    ];

    public function __construct(
        RendererInterface $renderer,
        Repository $repository,
        Router $router,
        FlashService $flash
    ) {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->repository = $repository;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
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
     * List elements
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->repository->findPaginated(15, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath . '/index', compact('items'));
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
     * Edit element
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->repository->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->repository->update($item->id, $params);
                $this->flash->addFlash('success', $this->messages['edit']);

                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $item->content = $params['content'];
            $item->name = $params['name'];
            $item->slug = $params['slug'];
        }

        return $this->renderer->render($this->viewPath . '/edit', $this->formParams(compact('item', 'errors')));
    }

    /**
     * Create a new element
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->repository->insert($params);
                $this->flash->addFlash('success', $this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $item = $params;
            $errors = $validator->getErrors();
        }

        return $this->renderer->render($this->viewPath . '/create', $this->formParams(compact('item', 'errors')));
    }

    public function delete(Request $request)
    {
        $this->repository->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }

    protected function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(Request $request)
    {
        return new Validator($request->getParsedBody());
    }

    /**
     * @return array
     */
    protected function getNewEntity()
    {
        return [];
    }

    /**
     * Allow to handle params to send to the view.
     * @param $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
