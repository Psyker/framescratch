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
    RendererInterface::class => factory(TwigRendererFactory::class),
    \PDO::class => function(\Psr\Container\ContainerInterface $container ) {
        return $pdo = new PDO(
            'mysql:host='. $container->get('database.host').
            ';dbname=' . $container->get('database.name'),
            $container->get('database.user'),
            $container->get('database.pass'),
            [
                PDO::FETCH_OBJ,
                PDO::ERRMODE_EXCEPTION
            ]
         );
    }
];