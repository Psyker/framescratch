<?php

use App\Blog\BlogModule;
use Framework\App;

require '../vendor/autoload.php';

$renderer = new \Framework\Renderer();
$renderer->addPath(dirname(__DIR__) .  '/views');

$app = new App([
    BlogModule::class
], [
    'renderer' => $renderer
]);

$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

\Http\Response\send($response);
