<?php

namespace Tests\Framework\Modules;

use Framework\Router;

class ErroredModule
{
    public function __construct(Router $router)
    {
        $router->get('/fake', function() {
            return new \stdClass();
        }, 'fake');
    }
}
