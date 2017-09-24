<?php

namespace Framework;

class Renderer
{
    const DEFAULT_NAMESPACE = '__MAIN';

    private $paths = [];

    /**
     * Globally accessible variable from all the views
     * @var array
     */
    private $globals = [];

    /**
     * Add path to load views.
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null) : void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
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
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view). '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view .'.php';
        }

        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
    }

    /**
     * Add globals variable to all the views.
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') -1);
    }

    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@'. $namespace, $this->paths[$namespace], $view);
    }
}
