<?php

use App\Admin\AdminModule;
use App\Admin\AdminTwigExtension;
use App\Admin\DashboardAction;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => [],
    AdminTwigExtension::class => \DI\object()->constructor(\DI\get('admin.widgets')),
    AdminModule::class => \DI\object()->constructorParameter('prefix', \DI\get('admin.prefix')),
    DashboardAction::class => \DI\object()->constructorParameter('widgets', \DI\get('admin.widgets'))
];
