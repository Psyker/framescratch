<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use function \DI\{object, factory, get};
use Framework\Router;
use Framework\Router\RouterTwigExtension;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Twig\FlashExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\PagerFantaExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;
use Psr\Container\ContainerInterface;

return [
    'database.host' => 'localhost',
    'database.user' => 'root',
    'database.pass' => 'penchaksilat',
    'database.name'=> 'framescratch',
    'views.path' => dirname(__DIR__) .  '/views',
    'twig.extensions' => array(
        get(FlashExtension::class),
        get(RouterTwigExtension::class),
        get(PagerFantaExtension::class),
        get(TextExtension::class),
        get(TimeExtension::class),
        get(FormExtension::class)
    ),
    SessionInterface::class => object(PHPSession::class),
    Router::class => object(),
    RendererInterface::class => factory(TwigRendererFactory::class),
    \PDO::class => function(ContainerInterface $container ) {
        return $pdo = new PDO(
            'mysql:host='. $container->get('database.host').
            ';dbname=' . $container->get('database.name'),
            $container->get('database.user'),
            $container->get('database.pass'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
         );
    }
];