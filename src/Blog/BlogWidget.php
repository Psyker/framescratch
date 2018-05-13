<?php
 namespace App\Blog;

 use App\Admin\AdminWidgetInterface;
 use App\Blog\Repository\PostRepository;
 use Framework\Renderer\RendererInterface;

class BlogWidget implements AdminWidgetInterface
{

    /**
      * @var RendererInterface
      */
    private $renderer;
    /**
      * @var PostRepository
      */
    private $postRepository;

    public function __construct(RendererInterface $renderer, PostRepository $postRepository)
    {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
    }

    public function render(): string
    {
        $count = $this->postRepository->count();
        return $this->renderer->render('@blog/admin/widget', compact('count'));
    }

    public function renderMenu(): string
    {
        return $this->renderer->render('@blog/admin/menu');
    }
}
