<?php

namespace Framework\Renderer;

interface RendererInterface
{
    /**
     * Add path to load views.
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Render a view.
     * The path can be specified with namespaces from addPath()
     * $this->render('@blog/view')
     * $this->render('view')
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

    /**
     * Add globals variable to all the views.
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void;
}
