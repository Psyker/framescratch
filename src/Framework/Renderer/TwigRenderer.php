<?php

namespace Framework\Renderer;

class TwigRenderer implements RendererInterface
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Twig_Loader_Filesystem
     */
    private $loader;

    public function __construct(string $path)
    {
        $this->loader = new \Twig_Loader_Filesystem($path);
        $this->twig = new \Twig_Environment($this->loader, []);
    }

    /**
     * Add path to load views.
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Render a view.
     * The path can be specified with namespaces from addPath()
     * $this->render('@blog/view')
     * $this->render('view')
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return  $this->twig->render($view . '.html.twig', $params);
    }

    /**
     * Add globals variable to all the views.
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
