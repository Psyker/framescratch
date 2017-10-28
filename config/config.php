<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use function \DI\{object, factory, get};
use Framework\Router;
use Framework\Router\RouterTwigExtension;

return [
    'database.host' => 'localhost',
    'database.user' => 'root',
    'database.pass' => 'penchaksilat',
    'database.name'=> 'framescratch',
    'views.path' => dirname(__DIR__) .  '/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class)
    ],
    Router::class => object(),
    RendererInterface::class => factory(TwigRendererFactory::class)
];