<?php

use App\Blog\BlogModule;
use Framework\App;

require dirname(__DIR__) . '/vendor/autoload.php';

$modules = [
    BlogModule::class
];

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . "/config/config.php");
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$builder->addDefinitions(dirname(__DIR__) . "/config.php");
$container = $builder->build();

$app = new App($container, $modules);
if (php_sapi_name() != "cli") {
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
