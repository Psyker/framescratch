<?php

namespace Framework\Renderer;

use Framework\Router\RouterTwigExtension;
use Psr\Container\ContainerInterface;
use Twig\Extension\DebugExtension;

class TwigRendererFactory
{

    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $viewPath = $container->get('views.path');
        $loader = new \Twig_Loader_Filesystem($viewPath);
        $twig = new \Twig_Environment($loader, ['debug' => true]);
        $twig->addExtension(new DebugExtension());
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        return new TwigRenderer($twig);
    }
}
